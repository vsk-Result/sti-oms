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
use App\Services\DebtService;
use App\Services\OrganizationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportObjectDebtsFromExcel extends Command
{
    protected $signature = 'oms-imports:objects-debts-from-excel';

    protected $description = 'Загружает долги по подрядчикам объектов из Excel';

    private OrganizationService $organizationService;
    private Sanitizer $sanitizer;
    private DebtService $debtService;

    public function __construct(OrganizationService $organizationService, Sanitizer $sanitizer, DebtService $debtService)
    {
        parent::__construct();
        $this->organizationService = $organizationService;
        $this->sanitizer = $sanitizer;
        $this->debtService = $debtService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Загрузка долгов по подрядчикам объектов из Excel');

        $availableCodes = ['346', '349', '353', '358', '359', '360'];

        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if (!$company) {
            return 'В системе ОМС не найдена компания ООО "Строй Техно Инженеринг"';
        }

        $import = DebtImport::create([
            'type_id' => DebtImport::TYPE_OBJECT,
            'company_id' => $company->id,
            'date' => Carbon::now(),
            'file' => '',
            'status_id' => Status::STATUS_ACTIVE
        ]);

        foreach ($availableCodes as $code) {
            Log::channel('custom_imports_log')->debug('[INFO] Загружается файл ' . $code . '.xls');

            $importFilePath = storage_path() . '/app/public/public/objects-debts/' . $code . '.xlsx';

            if (! File::exists($importFilePath)) {
                $importFilePath = storage_path() . '/app/public/public/objects-debts/' . $code . '.xls';

                if (! File::exists($importFilePath)) {
                    Log::channel('custom_imports_log')->debug('[ERROR] Файл для загрузки "' . $importFilePath . '" не найден');
                    continue;
                }
            }

            try {
                $importData = Excel::toArray(new ObjectImport(), $importFilePath);

                if (!isset($importData['OMS'])) {
                    Log::channel('custom_imports_log')->debug('[ERROR] В загружаемом файле отсутствует лист OMS');
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

                    if (empty($amountDebt)) {
                        $amountDebt = 0;
                    }
                    if (empty($guarantee)) {
                        $guarantee = 0;
                    }
                    if (empty($avans)) {
                        $avans = 0;
                    }

                    $organization = $this->organizationService->getOrCreateOrganization([
                        'inn' => null,
                        'name' => $organizationName,
                        'company_id' => null,
                        'kpp' => null
                    ]);

                    $object = BObject::where('code', $code)->first();

                    $this->debtService->createDebt([
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
                        'amount_without_nds' => -($amountDebt - ($amountDebt / 6)),
                        'status_id' => Status::STATUS_ACTIVE,
                        'category' => '',
                        'code' => '',
                        'invoice_number' => '',
                        'order_author' => '',
                        'description' => '',
                        'comment' => '',
                        'contract' => $contract,
                        'invoice_payment_due_date' => null,
                        'invoice_amount' => 0
                    ]);
                }

            } catch (\Exception $e) {
                Log::channel('custom_imports_log')->debug('[ERROR] Не удалось загрузить файл: "' . $e->getMessage());
            }

            try {
                $newFileName = $import->id . '_' . $code . '.xlsx';
                Storage::putFileAs('debt-imports/objects', $importFilePath, $newFileName);
            } catch (\Exception $e) {
                Log::channel('custom_imports_log')->debug('[ERROR] Не удалось сохранить файл: "' . $e->getMessage());
                return 0;
            }
        }

        $objectIds = BObject::whereIn('code', $availableCodes)->pluck('id')->toArray();
        DebtManual::whereIn('object_id', $objectIds)->delete();

        Log::channel('custom_imports_log')->debug('[SUCCESS] Импорт прошел успешно');

        return 0;
    }
}
