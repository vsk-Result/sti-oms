<?php

namespace App\Console\Commands;

use App\Helpers\Sanitizer;
use App\Imports\DebtImport\ObjectImport;
use App\Models\Company;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Services\CRONProcessService;
use App\Services\DebtService;
use App\Services\OrganizationService;
use App\Services\PivotObjectDebtService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportObjectDebtsFromExcel extends Command
{
    protected $signature = 'oms:objects-debts-from-excel';

    protected $description = 'Загружает долги по подрядчикам объектов из Excel';

    private OrganizationService $organizationService;
    private Sanitizer $sanitizer;
    private DebtService $debtService;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(
        OrganizationService $organizationService,
        Sanitizer $sanitizer,
        DebtService $debtService,
        CRONProcessService $CRONProcessService,
        PivotObjectDebtService $pivotObjectDebtService
    ) {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 19:00'
        );
        $this->organizationService = $organizationService;
        $this->sanitizer = $sanitizer;
        $this->debtService = $debtService;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Загрузка долгов по подрядчикам объектов из Excel');

        $availableCodes = BObject::getCodesForContractorImportDebts();

        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if (!$company) {
            $errorMessage = 'В системе ОМС не найдена компания ООО "Строй Техно Инженеринг"';
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        $prevImport = DebtImport::where('date', Carbon::now()->format('Y-m-d'))
            ->where('type_id', DebtImport::TYPE_OBJECT)
            ->where('company_id', $company->id)
            ->where('status_id', Status::STATUS_ACTIVE)
            ->first();

        $import = DebtImport::create([
            'type_id' => DebtImport::TYPE_OBJECT,
            'company_id' => $company->id,
            'date' => Carbon::now(),
            'file' => '',
            'status_id' => Status::STATUS_ACTIVE
        ]);

        foreach ($availableCodes as $code) {
            Log::channel('custom_imports_log')->debug('[INFO] Загружается файл ' . $code . '.xlsx');

            // Путь до файла с автоматической загрузкой
            $importFilePath = storage_path() . '/app/public/public/objects-debts/' . $code . '.xlsx';

            //Путь до файла с ручной загрузкой
            $importManualFilePath = storage_path() . '/app/public/public/objects-debts-manuals/' . $code . '.xlsx';

            if (! File::exists($importFilePath)) {
                Log::channel('custom_imports_log')->debug('[INFO] Не загрузился файл ' . $code . '.xlsx, загружается файл ' . $code . '.xls');

                $importFilePath = storage_path() . '/app/public/public/objects-debts/' . $code . '.xls';

                if (! File::exists($importFilePath)) {
                    $errorMessage = '[ERROR] Файл для загрузки "' . $importFilePath . '" не найден, загружается ручной файл ' . $code . '.xlsx';
                    Log::channel('custom_imports_log')->debug($errorMessage);

                    if (! File::exists($importManualFilePath)) {
                        $errorMessage = '[ERROR] Файл для загрузки "' . $importFilePath . '" не найден';
                        Log::channel('custom_imports_log')->debug($errorMessage);
                        $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
//                        return 0;
                        continue;
                    }
                }
            }

            // Самый новый файл будет являться актуальным при загрузке
            if (! File::exists($importFilePath)) {
                $importFilePath = $importManualFilePath;
            } else {
                if (File::exists($importManualFilePath)) {
                    if (File::lastModified($importManualFilePath) > File::lastModified($importFilePath)) {
                        $importFilePath = $importManualFilePath;
                    }
                }
            }

            try {
                $importData = Excel::toArray(new ObjectImport(), $importFilePath);

                if (!isset($importData['OMS'])) {
                    $errorMessage = '[ERROR] В загружаемом файле отсутствует лист OMS';
                    Log::channel('custom_imports_log')->debug($errorMessage);
                    $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
                    continue;
                }

                foreach ($importData['OMS'] as $row) {
                    if (empty($row[0]) || $row[0] === 'Контрагент' || $row[0] === 'Общий итог' || $row[0] === 'Итого') {
                        continue;
                    }

                    $organizationName = $this->sanitizer->set($row[0])->get();
                    $contract = $this->sanitizer->set($row[1])->get();
                    $amountDebt = $row[2];
                    $guarantee = $row[3];
                    $avans = $row[4];
                    $nds = mb_strtolower($row[5] ?? '');
                    $balanceContract = $row[6] ?? 0;

                    if (empty($amountDebt)) {
                        $amountDebt = 0;
                    }
                    if (empty($guarantee)) {
                        $guarantee = 0;
                    }
                    if (empty($avans)) {
                        $avans = 0;
                    }

                    if (is_string($amountDebt)) {
                        $amountDebt = str_replace(',', '.', $amountDebt);
                        $amountDebt = (float) str_replace(' ', '', $amountDebt);
                    }
                    if (is_string($guarantee)) {
                        $guarantee = str_replace(',', '.', $guarantee);
                        $guarantee = (float) str_replace(' ', '', $guarantee);
                    }
                    if (is_string($avans)) {
                        $avans = str_replace(',', '.', $avans);
                        $avans = (float) str_replace(' ', '', $avans);
                    }
                    if (is_string($balanceContract)) {
                        $balanceContract = str_replace(',', '.', $balanceContract);
                        $balanceContract = (float) str_replace(' ', '', $balanceContract);
                    }

                    $amountDebtWithoutDNS = -$amountDebt;
                    if (! empty($nds)) {
                        if ($nds === 'ндс' || $nds === 'с ндс') {
                            $amountDebtWithoutDNS = -($amountDebt - ($amountDebt / 6));
                        }
                    }

                    $organization = $this->organizationService->getOrCreateOrganization([
                        'inn' => null,
                        'name' => $organizationName,
                        'company_id' => null,
                        'kpp' => null
                    ]);

                    $object = BObject::where('code', $code)->first();

                    $debt = $this->debtService->createDebt([
                        'import_id' => $import->id,
                        'type_id' => Debt::TYPE_CONTRACTOR,
                        'company_id' => $import->company_id,
                        'object_id' => $object->id,
                        'object_worktype_id' => null,
                        'organization_id' => $organization->id,
                        'date' => $import->date,
                        'amount' => -$amountDebt,
                        'guarantee' => -$guarantee,
                        'avans' => -$avans,
                        'amount_without_nds' => $amountDebtWithoutDNS,
                        'status_id' => Status::STATUS_ACTIVE,
                        'category' => '',
                        'code' => '',
                        'invoice_number' => '',
                        'order_author' => '',
                        'description' => '',
                        'comment' => '',
                        'contract' => $contract,
                        'invoice_payment_due_date' => null,
                        'invoice_amount' => 0,
                        'balance_contract' => -$balanceContract,
                    ]);
                }

            } catch (\Exception $e) {
                $errorMessage = '[ERROR] Не удалось загрузить файл: "' . $e->getMessage();
                Log::channel('custom_imports_log')->debug($errorMessage);
                $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
                continue;
            }

            try {
                $newFileName = $import->id . '_' . $code . '.xlsx';
                Storage::putFileAs('debt-imports/objects', $importFilePath, $newFileName);
            } catch (\Exception $e) {
                $errorMessage = '[ERROR] Не удалось сохранить файл: "' . $e->getMessage();
                Log::channel('custom_imports_log')->debug($errorMessage);
                $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
                continue;
            }
        }

        $objectIds = BObject::whereIn('code', $availableCodes)->pluck('id')->toArray();
        DebtManual::whereIn('object_id', $objectIds)->delete();

        Log::channel('custom_imports_log')->debug('[SUCCESS] Импорт прошел успешно');
        $this->CRONProcessService->successProcess($this->signature);

        if ($prevImport) {
            $prevImport->delete();
        }

        $this->pivotObjectDebtService->updatePivotDebtForAllObjects();

        return 0;
    }
}
