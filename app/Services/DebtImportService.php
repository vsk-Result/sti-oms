<?php

namespace App\Services;

use App\Imports\DebtImport\Import;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Models\Status;
use Maatwebsite\Excel\Facades\Excel;

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
        'Башня Эволюция' => '268',
        'Скандинавский' => '335',
        'Мост' => '317',
        'Спуск' => '325',
        'Эвалар-2' => '352',
        'Ордынка' => '304',
        'Пречистенка' => '298',
        'НИИ Транснефть' => '257',
        'Москва' => '1',
        'Гута' => '332',
        'Садовые кварталы' => '321',
        'Роза Росса' => '350',
        'Сухаревская' => '353',
        'Погодинка' => '327',
        'ЦМТ' => '322',
        'Москва  ' => '1',
        'Москва (Склад)' => '1',
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
            $this->createSupplyImport($importData['Для фин отчёта'], $requestData);
        } elseif (isset($importData['ДТТЕРМО'])) {
            $this->createDTTermoImport($importData['ДТТЕРМО'], $requestData);
        }
    }

    public function destroyImport(DebtImport $import): DebtImport
    {
        $import->delete();

        foreach ($import->debts as $debt) {
            $this->debtService->destroyDebt($debt);
        }

        return $import;
    }

    private function createSupplyImport(array $importData, array $requestData): void
    {
        unset($importData[0], $importData[1], $importData[2]);

        $availableWorktypes = [null, '0', '1', '2', '3', '4', '5', '6', '7', '8'];

        $isContractor = true;
        $providerIndex = null;
        $contractorIndex = 0;
        $providersWorktypes = [];
        $contractorsWorktypes = [];

        foreach ($importData[3] as $index => $possibleWorktype) {
            if ($possibleWorktype === 'Общий итог') {
                if (! $isContractor) {
                    break;
                }
                $isContractor = false;
                $providerIndex = $index + 2;
            } else if (in_array($possibleWorktype, $availableWorktypes)) {
                if ($possibleWorktype === '!') {
                    $possibleWorktype = 1;
                }
                $possibleWorktype = (int) $possibleWorktype;
                if ($possibleWorktype === 0) {
                    $possibleWorktype = 1;
                }
                if ($isContractor) {
                    $contractorsWorktypes[$index] = $possibleWorktype;
                } else {
                    $providersWorktypes[$index] = $possibleWorktype;
                }
            }
        }

        unset($importData[3]);

        $providers = $this->getOrganizationsFromImportData($importData, $providerIndex, $providersWorktypes);
        $contractors = $this->getOrganizationsFromImportData($importData, $contractorIndex, $contractorsWorktypes);

        $import = DebtImport::create([
            'type_id' => DebtImport::TYPE_SUPPLY,
            'company_id' => $requestData['company_id'],
            'date' => $requestData['date'],
            'file' => $this->uploadService->uploadFile('debt-imports', $requestData['file']),
            'status_id' => Status::STATUS_ACTIVE
        ]);

        $this->createDebts($import, $providers, Debt::TYPE_PROVIDER);
        $this->createDebts($import, $contractors, Debt::TYPE_CONTRACTOR);
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
            'name' => 'ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ "ДТ ТЕРМО ГРУПП"',
            'company_id' => null,
            'kpp' => null
        ]);

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
            ) {
                $code = substr($value, 0, strpos($value, '/'));
                $code = str_replace(' ', '', $code);
                $code = str_replace(',', '.', $code);

                if (in_array($code, $badCodes)) {
                    continue;
                }

                $worktype = null;

                if ($code == '27' || $code == '27.1' || $code == '27.7') {
                    $code = '1';
                } elseif ($code == '27.2') {
                    $code = '2';
                } elseif ($code == '27.3') {
                    $code = '4';
                } elseif ($code == '27.4') {
                    $code = '3';
                } elseif ($code == '27.8') {
                    $code = '5';
                } elseif ($code == '28') {
                    $code = '28';
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

    private function getOrganizationsFromImportData(array $importData, int $organizationIndex, array $worktypes): array
    {
        $organizations = [];
        $currentObject = null;
        foreach ($importData as $data) {
            $value = $data[$organizationIndex];

            if (in_array($value, ['', '(пусто)', 'Общий итог'])) {
                continue;
            }

            if (array_key_exists($value, $this->objects)) {
                $currentObject = $this->objects[$value];
            } else {
                foreach ($worktypes as $index => $worktype) {
                    if (! isset($organizations[$value][$currentObject][$worktype])) {
                        $organizations[$value][$currentObject][$worktype] = 0;
                    }
                    $organizations[$value][$currentObject][$worktype] += $data[$index];
                }
            }
        }

        return $organizations;
    }

    private function createDebts(DebtImport $import, array $organizations, int $typeId): void
    {
        foreach ($organizations as $organizationName => $objectCodes) {

            if ($organizationName === 'ДТ Термо') {
                continue;
            }

            $organization = $this->organizationService->getOrCreateOrganization([
                'inn' => null,
                'name' => $organizationName,
                'company_id' => null,
                'kpp' => null
            ]);

            foreach ($objectCodes as $code => $worktypes) {

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

                foreach ($worktypes as $worktype => $amount) {
                    if ($amount === 0) {
                        continue;
                    }
                    $this->debtService->createDebt([
                        'import_id' => $import->id,
                        'type_id' => $typeId,
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
}
