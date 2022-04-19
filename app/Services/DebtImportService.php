<?php

namespace App\Services;

use App\Imports\DebtImport\Import;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Models\Status;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DebtImportService
{
    private DebtService $debtService;
    private UploadService $uploadService;
    private ObjectService $objectService;
    private OrganizationService $organizationService;

    private array $objects = [
        'ГЭС-2' => '288',
        'Бродский' => '338',
        'Октафарма' => '346',
        'Новороссийск' => '354',
        'Николино' => '344',
        'Ритц' => '342',
        'Патриот' => '349',
        'Патриот ' => '349',
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
        'Сухаревская' => '353',
        'Погодинка' => '327',
        'ЦМТ' => '322',
        'Москва  ' => '27.1',
        'Москва (Склад)' => '27.1',
        'Бамбилэнд' => '279',
        'Скопин' => '346',
        'Башня Федерации' => '292',
        'ВТБ Арена Парк' => '355',
        'Динамо' => '355',
        'Белые столбы' => '308',
        'Реномэ' => '323',
        'Боско' => '309',
        'Эвалар' => '352',
        'Пестово' => '320',
        'Прачечная' => '357',
        'Мост ' => '317',
        'Мос' => '317',
        'Завидово' => '358',
        'ДТ-Термо' => '27.1',
    ];

    public function __construct(
        DebtService $debtService,
        UploadService $uploadService,
        ObjectService $objectService,
        OrganizationService $organizationService
    ) {
        $this->debtService = $debtService;
        $this->uploadService = $uploadService;
        $this->objectService = $objectService;
        $this->organizationService = $organizationService;
    }

    public function createImport(array $requestData): void
    {
        $importData = Excel::toArray(new Import(), $requestData['file']);

        if (isset($importData['Для фин отчёта'])) {
            $this->createSupplyImport($importData, $requestData);
        } elseif (isset($importData['ДТТЕРМО'])) {
            $this->createDTTermoImport($importData['ДТТЕРМО'], $requestData);
        }
    }

    public function destroyImport(DebtImport $import): DebtImport
    {
        $import->delete();
        $import->debts->delete();

        return $import;
    }

    private function createSupplyImport(array $importData, array $requestData): void
    {
        $import = DebtImport::create([
            'type_id' => DebtImport::TYPE_SUPPLY,
            'company_id' => $requestData['company_id'],
            'date' => $requestData['date'],
            'file' => $this->uploadService->uploadFile('debt-imports', $requestData['file']),
            'status_id' => Status::STATUS_ACTIVE
        ]);

        $providers = $importData['МАТЕРИАЛЫ'];
        $contractors = $importData['ПОДРЯДЧИКИ'];

        // МАТЕРИАЛЫ
        unset($providers[0], $providers[1], $providers[2]);

        foreach ($providers as $row) {
            if (is_null($row[1]) && is_null($row[2])) {
                break;
            }

            $organizationName = trim($row[3]);
            $objectName = trim($row[10]);

            if ($organizationName === 'ДТ Термо') {
                continue;
            }

            $organization = $this->organizationService->getOrCreateOrganization([
                'inn' => null,
                'name' => $organizationName,
                'company_id' => null,
                'kpp' => null
            ]);

            $object = BObject::where('code', $this->objects[$objectName])->first();
            if (! $object) {
                $code = str_replace(',', '.', $row[8]);
                $code = substr($code, 0, strpos($code, '.'));
                $object = $this->objectService->createObject([
                    'code' => $code,
                    'name' => $objectName,
                    'address' => null,
                    'responsible_name' => null,
                    'responsible_email' => null,
                    'responsible_phone' => null,
                    'photo' => null,
                ]);
            }

            $dueDate = null;
            $comment = trim($row[14]);
            if (is_numeric($row[13])) {
                $dueDate = Carbon::parse(Date::excelToDateTimeObject($row[13]))->format('Y-m-d');
            } else {
                if (! empty($row[13])) {
                    $comment .= ' СРОК ОПЛАТЫ СЧЁТА: ' . $row[13];
                }
            }
            $this->debtService->createDebt([
                'import_id' => $import->id,
                'type_id' => Debt::TYPE_PROVIDER,
                'company_id' => $import->company_id,
                'object_id' => $object->id,
                'object_worktype_id' => $row[15],
                'organization_id' => $organization->id,
                'date' => $import->date,
                'amount' => -$row[12],
                'amount_without_nds' => -($row[12] - ($row[12] / 6)),
                'status_id' => Status::STATUS_ACTIVE,
                'category' => trim($row[2]),
                'code' => trim($row[9]),
                'invoice_number' => trim($row[5]) . ' от ' . Carbon::parse(Date::excelToDateTimeObject($row[6]))->format('d/m/Y'),
                'order_author' => trim($row[1]),
                'description' => trim($row[4]),
                'comment' => $comment,
                'invoice_payment_due_date' => $dueDate,
                'invoice_amount' => $row[7] ?? 0
            ]);
        }

        // ПОДРЯДЧИКИ
        unset($contractors[0], $contractors[1], $contractors[2]);

        foreach ($contractors as $row) {
            if (is_null($row[1]) && is_null($row[2])) {
                break;
            }

            $organizationName = trim($row[3]);
            $objectName = trim($row[10]);

            if ($organizationName === 'ДТ Термо' || empty($row[12])) {
                continue;
            }

            $organization = $this->organizationService->getOrCreateOrganization([
                'inn' => null,
                'name' => $organizationName,
                'company_id' => null,
                'kpp' => null
            ]);

            $object = BObject::where('code', $this->objects[$objectName])->first();
            if (! $object) {
                $code = str_replace(',', '.', $row[8]);
                $code = substr($code, 0, strpos($code, '.'));
                $object = $this->objectService->createObject([
                    'code' => $code,
                    'name' => $objectName,
                    'address' => null,
                    'responsible_name' => null,
                    'responsible_email' => null,
                    'responsible_phone' => null,
                    'photo' => null,
                ]);
            }

            $dueDate = null;
            $comment = trim($row[16]);
            if (is_numeric($row[13])) {
                $dueDate = Carbon::parse(Date::excelToDateTimeObject($row[13]))->format('Y-m-d');
            } else {
                if (! empty($row[13])) {
                    $comment .= ' СРОК ОПЛАТЫ СЧЁТА: ' . $row[13];
                }
            }

            if (! empty($row[15])) {
                $comment .= ' АКТ ЗА МЕСЯЦ: ' . $row[15];
            }

            $this->debtService->createDebt([
                'import_id' => $import->id,
                'type_id' => Debt::TYPE_CONTRACTOR,
                'company_id' => $import->company_id,
                'object_id' => $object->id,
                'object_worktype_id' => $row[17],
                'organization_id' => $organization->id,
                'date' => $import->date,
                'amount' => -$row[12],
                'amount_without_nds' => -($row[12] - ($row[12] / 6)),
                'status_id' => Status::STATUS_ACTIVE,
                'category' => trim($row[14]),
                'code' => trim($row[9]),
                'invoice_number' => trim($row[5]) . ' от ' . Carbon::parse(Date::excelToDateTimeObject($row[6]))->format('d/m/Y'),
                'order_author' => trim($row[1]),
                'description' => trim($row[4]),
                'comment' =>  $comment,
                'invoice_payment_due_date' => $dueDate,
                'invoice_amount' => $row[7] ?? 0
            ]);
        }
    }

    private function createDTTermoImport(array $importData, array $requestData): void
    {
        $badCodes = ['27', '41', '28', '32', '36', '28', '27.3', '27.8'];
        unset($importData[0], $importData[1], $importData[2], $importData[3], $importData[4], $importData[5], $importData[6]);

        $import = DebtImport::create([
            'type_id' => DebtImport::TYPE_DTTERMO,
            'company_id' => $requestData['company_id'],
            'date' => $requestData['date'],
            'file' => $this->uploadService->uploadFile('debt-imports', $requestData['file_original']),
            'status_id' => Status::STATUS_ACTIVE
        ]);

        $organization = $this->organizationService->getOrCreateOrganization([
            'inn' => null,
            'name' => 'ООО "ДТ ТЕРМО ГРУПП"',
            'company_id' => null,
            'kpp' => null
        ]);

        $codesWithoutWorktype = BObject::getCodesWithoutWorktype();

        foreach ($importData as $row) {
            $value = $row[0];
            $amount = $row[6];

            if ($value === null || $value === 'Итого по отчету') {
                continue;
            }

            if (
                strpos($value, '/') !== false
                && strpos($value, 'Договор') === false
                && $amount !== null
                && $amount !== 0
                && $amount !== ''
            ) {
                $code = substr($value, 0, strpos($value, '/'));
                $code = str_replace(' ', '', $code);
                $code = str_replace(',', '.', $code);

                if (in_array($code, $badCodes)) {
                    continue;
                }

                $worktype = null;

                if (isset($codesWithoutWorktype[$code])) {
                    $code = $codesWithoutWorktype[$code];
                } else {
                    $oCode = $code;

                    if (str_contains($code, '.')) {
                        $code = substr($code, 0, strpos($code, '.'));
                        $worktype = (int) substr($oCode, strpos($oCode, '.') + 1);
                    }
                }

                $object = BObject::where('code', $code)->first();
                if (! $object) {
                    $object = $this->objectService->createObject([
                        'code' => $code,
                        'name' => 'Без названия',
                        'address' => null,
                        'responsible_name' => null,
                        'responsible_email' => null,
                        'responsible_phone' => null,
                        'photo' => null,
                    ]);
                }

                $this->debtService->createDebt([
                    'import_id' => $import->id,
                    'type_id' => Debt::TYPE_PROVIDER,
                    'company_id' => $import->company_id,
                    'object_id' => $object->id,
                    'object_worktype_id' => $worktype,
                    'organization_id' => $organization->id,
                    'date' => $import->date,
                    'amount' => -$amount,
                    'amount_without_nds' => -($amount - ($amount / 6)),
                    'status_id' => Status::STATUS_ACTIVE,
                ]);
            }
        }
    }
}
