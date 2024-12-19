<?php

namespace App\Console\Commands;

use App\Helpers\Sanitizer;
use App\Imports\DebtImport\Import;
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
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportObjectDebtsFromExcel extends Command
{
    protected $signature = 'oms:objects-debts-from-excel';

    protected $description = 'Загружает долги по подрядчикам объектов из Excel';

    private OrganizationService $organizationService;
    private Sanitizer $sanitizer;
    private DebtService $debtService;
    private PivotObjectDebtService $pivotObjectDebtService;

    private array $objects = [
        'Бамиленд' => '279',
        'Боско' => '309',
        'Белые столбы' => '308',
        'Пестово' => '320',
        'Реномэ' => '323',
        'Погодинка' => '327',
        'ДТ Термо' => '27.1',
        'Общий' => '27.1',
        'ЦМТ' => '322',
        'Бродский' => '338',
        'Октафарма' => '346',
        'Октофарма' => '346',
        'Новороссийск' => '354',
        'Николино' => '344',
        'Ритц' => '342',
        'Патриот' => '349',
        'Патриот ' => '349',
        'патриот' => '349',
        'Париот' => '349',
        'Башня Эволюция' => '268',
        'Скандинавский' => '335',
        'Мост' => '317',
        'Спуск' => '325',
        'Эвалар-2' => '352',
        'Ордынка' => '304',
        'Пречистенка' => '298',
        'НИИ Транснефть' => '257',
        'Москва' => '27.1',
        'Гута' => '332',
        'Садовые кварталы' => '321',
        'Роза Росса' => '350',
        'Роза Роса' => '350',
        'Сухаревская' => '353',
        'Погодинская' => '327',
        'Москва  ' => '27.1',
        'Склад СТИ' => '27.1',
        'Москва (Склад)' => '27.1',
        'Скопин' => '346',
        'Башня Федерации' => '292',
        'ВТБ Арена Парк' => '355',
        'Динамо' => '355',
        'Эвалар' => '352',
        'Прачечная' => '288',
        'ГЭС-2' => '288',
        'Мост ' => '317',
        'Мос' => '317',
        'Завидово' => '358',
        'ДТ-Термо' => '27.1',
        'Магнитогорск' => '359',
        'Магнитогорск (Ф)' => '359',
        'Магнитогорск (И)' => '359',
        'Магнитогорск (ф)' => '359',
        'Магнитогорск (и)' => '359',
        'Магнитогорск Ф' => '359',
        'Магнитогорск И' => '359',
        'Магнитогорск ф' => '359',
        'Магнитогорск и' => '359',
        'Магнитогорск ИЦ' => '359',
        'Магнитогорск.И' => '359',
        'Магнитогорск.Ф' => '359',
        'Тинькофф' => '360',
        'Домодедово' => '308',
        'Кастанаевская' => '303',
        '367 Офис Веспер' => '367',
        '371. ЗИЛ' => '371',
        '372. Офис Веспер Тверская' => '372',
    ];

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
            'Каждые 15 минут'
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

        $company = Company::getSTI();

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

        $objectCodesWithAmount = [];

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
                        $errorMessage = '[ERROR] Файл для загрузки "' . $importManualFilePath . '" не найден';
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

                if ($code === '000') {
                    $this->importDataForClosedObjects($import, $importData);
                    continue;
                }

                $organizationIndex = null;
                $contractIndex = null;
                $amountIndex = null;
                $guaranteeIndex = null;
                $neotrabotAvansIndex = null;
                $avansIndex = null;
                $ndsIndex = null;
                $balanceContractIndex = null;
                $innIndex = null;
                $guaranteeDeadlineIndex = null;

                foreach ($importData['OMS'] as $index => $row) {
                    if ($index === 0) {
                        $organizationIndex = $this->getTitleIndex('Контрагент', $row);
                        $contractIndex = $this->getTitleIndex('Договор', $row);
                        $amountIndex = $this->getTitleIndex('смр', $row);
                        $guaranteeIndex = $this->getTitleIndex('Гарантийное удержание', $row);
                        $neotrabotAvansIndex = $this->getTitleIndex('Неотработанный аванс', $row);
                        $avansIndex = $this->getTitleIndex('Авансы к оплате', $row);
                        $ndsIndex = $this->getTitleIndex('ндс', $row);
                        $balanceContractIndex = $this->getTitleIndex('Остаток к оплате по договору', $row);
                        $innIndex = $this->getTitleIndex('ИНН', $row);
                        $guaranteeDeadlineIndex = $this->getTitleIndex('срок наступил', $row);
                        continue;
                    }

                    if (empty($row[0]) || $row[0] === 'Контрагент' || $row[0] === 'Общий итог' || $row[0] === 'Итого') {
                        continue;
                    }

                    $organizationName = $this->sanitizer->set($row[$organizationIndex])->get();
                    $contract = $this->sanitizer->set($row[$contractIndex])->get();
                    $amountDebt = is_null($amountIndex) ? 0 : $row[$amountIndex];
                    $guarantee = is_null($guaranteeIndex) ? 0 : $row[$guaranteeIndex];
                    $avans = is_null($avansIndex) ? 0 : $row[$avansIndex];
                    $nds = is_null($ndsIndex) ? '' : mb_strtolower($row[$ndsIndex]);
                    $balanceContract = is_null($balanceContractIndex) ? 0 : $row[$balanceContractIndex];
                    $neotrabotAvans =  is_null($neotrabotAvansIndex) ? 0 : $row[$neotrabotAvansIndex];
                    $inn = is_null($innIndex) ? '' : trim($row[$innIndex]);
                    $guaranteeDeadline = is_null($guaranteeDeadlineIndex) ? 0 : $row[$guaranteeDeadlineIndex];

                    if (! is_null($amountIndex)) {
                        $objectCodesWithAmount[] = $code;
                    }

                    if (empty($amountDebt)) {
                        $amountDebt = 0;
                        $avans = 0;
                    }
                    if (empty($guarantee)) {
                        $guarantee = 0;
                    }
                    if (empty($avans)) {
                        $avans = 0;
                    }
                    if (empty($neotrabotAvans)) {
                        $neotrabotAvans = 0;
                    }
                    if (empty($guaranteeDeadline)) {
                        $guaranteeDeadline = 0;
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
                    if (is_string($neotrabotAvans)) {
                        $neotrabotAvans = str_replace(',', '.', $neotrabotAvans);
                        $neotrabotAvans = (float) str_replace(' ', '', $neotrabotAvans);
                    }
                    if (is_string($guaranteeDeadline)) {
                        $guaranteeDeadline = str_replace(',', '.', $guaranteeDeadline);
                        $guaranteeDeadline = (float) str_replace(' ', '', $guaranteeDeadline);
                    }

                    $amountDebtWithoutDNS = -$amountDebt;
                    if (! empty($nds)) {
                        if ($nds === 'ндс' || $nds === 'с ндс') {
                            $amountDebtWithoutDNS = -($amountDebt - ($amountDebt / 6));
                        }
                    }

                    $organization = $this->organizationService->getOrCreateOrganization([
                        'inn' => $inn,
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
                        'guarantee_deadline' => -$guaranteeDeadline,
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
                        'unwork_avans' => $neotrabotAvans,
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

        // Путь до файла с загрузкой долгов по подрядчикам из 1С
        $importFilePath = storage_path() . '/app/public/public/objects-debts-manuals/Raboty(XLSX).xlsx';

        try {
            $importData = Excel::toArray(new Import(), $importFilePath);

            $debtRows = $importData['TDSheet'];

            unset($debtRows[0]);

            foreach ($debtRows as $row) {
                $organizationName = trim($row[3]);
                $organizationType = trim($row[4]);
                $objectName = trim($row[11]);
                $amountDebt = trim($row[13]);
                $inn = trim($row[22] ?? '');
                $type = trim($row[23] ?? '');

                if ($type === 'Гарантийное удержание') {
                    continue;
                }

                if (empty($amountDebt)) {
                    continue;
                }

                if (empty($objectName)) {
                    continue;
                }

                if ($organizationType !== 'РАБОТЫ') {
                    continue;
                }

                $objectCode = $this->getObjectCodeFromFullName($objectName);

                if (in_array($objectCode, $objectCodesWithAmount)) {
                    continue;
                }

                $organization = $this->organizationService->getOrCreateOrganization([
                    'inn' => $inn,
                    'name' => $organizationName,
                    'company_id' => null,
                    'kpp' => null
                ]);


                if (! in_array($objectCode, $availableCodes)) {
                    continue;
                }

                $object = BObject::where('code', $objectCode)->first();
                if (! $object) {
                    $this->destroyImport($import);
                    return 'Объекта "' . $objectName . '" нет в системе ОМС.';
                }

                $dueDate = null;
                $comment = trim($row[18]);
                if (is_numeric($row[15])) {
                    $dueDate = Carbon::parse(Date::excelToDateTimeObject($row[15]))->format('Y-m-d');
                } else if (is_string($row[15])) {
                    try {
                        $dueDate = Carbon::parse($row[15])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $comment .= ' СРОК ОПЛАТЫ СЧЁТА: ' . $row[15];
                    }
                } else {
                    if (! empty($row[15])) {
                        $comment .= ' СРОК ОПЛАТЫ СЧЁТА: ' . $row[15];
                    }
                }

                if (! empty($row[16])) {
                    $comment .= ' АКТ ЗА МЕСЯЦ: ' . $row[16];
                }

                $comment .= ' || Валюта: ' . $row[14];

                $invoiceAmount = $row[8] ?? 0;
                $invoiceAmount = (string)$invoiceAmount;
                $invoiceAmount = str_replace(' ', '', $invoiceAmount);
                $invoiceAmount = str_replace(',', '.', $invoiceAmount);
                $invoiceAmount = (float)$invoiceAmount;

                $this->debtService->createDebt([
                    'import_id' => $import->id,
                    'type_id' => Debt::TYPE_CONTRACTOR,
                    'company_id' => $import->company_id,
                    'object_id' => $object->id,
                    'object_worktype_id' => null,
                    'organization_id' => $organization->id,
                    'date' => $import->date,
                    'amount' => $type !== 'Аванс' ? -$row[13] : 0,
                    'amount_without_nds' => $type !== 'Аванс' ? (-$row[13] ?? 0) : 0,
                    'avans' => $type === 'Аванс' ? -$row[13] : 0,
                    'status_id' => Status::STATUS_ACTIVE,
                    'category' => trim($row[16]),
                    'code' => trim($row[10]),
                    'invoice_number' => trim($row[6]) . ' от ' . $row[7],
                    'order_author' => trim($row[1]),
                    'description' => trim($row[2]) . ': ' . trim($row[5]),
                    'comment' => $comment,
                    'invoice_payment_due_date' => $dueDate,
                    'invoice_amount' => $invoiceAmount
                ]);
            }

        } catch (\Exception $e) {
            $errorMessage = '[ERROR] Не удалось загрузить файл Raboty(XLSX).xlsx: ' . $e->getMessage();
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
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

    public function importDataForClosedObjects(DebtImport $import, array $importData): void
    {
        foreach ($importData['OMS'] as $row) {
            if (empty($row[0]) || $row[0] === 'код объекта') {
                continue;
            }

            $organizationName = $this->sanitizer->set($row[1])->get();
            $contract = $this->sanitizer->set($row[2])->get();
            $amountDebt = 0;
            $guarantee = 0;
            $avans = 0;
            $balanceContract = 0;
            $neotrabotAvans = 0;

            $amountDebtWithoutDNS = -$amountDebt;

            if (!empty($row[4]) && $row[4] !== 'оплачено') {
                $guaranteeAmount = $row[4];
                if (is_string($guaranteeAmount)) {
                    $guaranteeAmount = str_replace(',', '.', $guaranteeAmount);
                    $guaranteeAmount = (float) str_replace(' ', '', $guaranteeAmount);
                }
                $guarantee += $guaranteeAmount;
            }

            if (!empty($row[6]) && $row[6] !== 'оплачено') {
                $guaranteeAmount = $row[6];
                if (is_string($guaranteeAmount)) {
                    $guaranteeAmount = str_replace(',', '.', $guaranteeAmount);
                    $guaranteeAmount = (float) str_replace(' ', '', $guaranteeAmount);
                }
                $guarantee += $guaranteeAmount;
            }

            if (!empty($row[8]) && $row[8] !== 'оплачено') {
                $guaranteeAmount = $row[8];
                if (is_string($guaranteeAmount)) {
                    $guaranteeAmount = str_replace(',', '.', $guaranteeAmount);
                    $guaranteeAmount = (float) str_replace(' ', '', $guaranteeAmount);
                }
                $guarantee += $guaranteeAmount;
            }

            $organization = $this->organizationService->getOrCreateOrganization([
                'inn' => null,
                'name' => $organizationName,
                'company_id' => null,
                'kpp' => null
            ]);

            $object = BObject::where('code', '000')->first();

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
                'description' => json_encode($row),
                'comment' => '',
                'contract' => $contract,
                'invoice_payment_due_date' => null,
                'invoice_amount' => 0,
                'balance_contract' => -$balanceContract,
                'unwork_avans' => $neotrabotAvans,
            ]);
        }
    }

    private function getObjectCodeFromFullName(string $objectName): string
    {
        if (isset($this->objects[$objectName])) {
            return $this->objects[$objectName];
        }

        return mb_substr($objectName, 0, mb_strpos($objectName, ','));
    }

    private function getTitleIndex(string $title, array $headers): int | null
    {
        foreach ($headers as $index => $header) {
            if (str_contains(mb_strtolower($header), mb_strtolower($title))) {
                return $index;
            }
        }

        return null;
    }
}
