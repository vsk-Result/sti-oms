<?php

namespace App\Services\DebtImport\Imports;

use App\Helpers\Sanitizer;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ManualReplaceImport as ManualReplaceImportExcelImport;
use Illuminate\Http\UploadedFile;

class ManualReplaceImport extends BaseImport
{
    public function __construct(string $filename, string $objectCode)
    {
        parent::__construct();

        $this->filename = $filename;
        $this->objectCode = $objectCode;
        $this->sanitizer = new Sanitizer();
    }

    public function import(): array
    {
        $importInfo = [
            'errors' => [],
            'data' => [],
        ];

        if ($this->fileToImportNotExist()) {
//            $importInfo['errors'][] = 'Файл для импорта долгов по подрядчикам "' . $this->filename . '" не найден';

            return $importInfo;
        }

        $object = BObject::where('code', $this->objectCode)->first();
        $importData = Excel::toArray(new ManualReplaceImportExcelImport(), new UploadedFile($this->getLatestFileToImport(), $this->filename));

        if (! isset($importData['Поставщикам']) || ! isset($importData['Подрядчикам']) || ! isset($importData['За услуги'])) {
            $importInfo['errors'][] = 'В загружаемом файле "' . $this->filename . '" не хватает листов с данными';

            return $importInfo;
        }

        $contractorImportData = $importData['Подрядчикам'];

        $importInfo['contractor']['data'][$object->id] = [
            'object_id' => $object->id,
            'object_name' => $object->name,
            'total_amount' => 0,
            'total_amount_without_nds' => 0,
            'total_guarantee' => 0,
            'total_avans' => 0,
            'total_guarantee_deadline' => 0,
            'total_balance_contract' => 0,
            'total_unwork_avans' => 0,
            'organizations' => []
        ];

        unset($contractorImportData[0]);
        $objectOrganizationExist = [];

        foreach ($contractorImportData as $row) {
            if (empty($row[0])) {
                continue;
            }

            $organizationName = $row[0];
            $inn = $row[1];
            $amountDebt = $this->prepareAmount($row[2] ?? 0);
            $avans = $this->prepareAmount($row[3] ?? 0);
            $neotrabotAvans =  $this->prepareAmount($row[4] ?? 0);
            $guarantee = $this->prepareAmount($row[5] ?? 0);
            $guaranteeDeadline = $this->prepareAmount($row[6] ?? 0);
            $balanceContract = $this->prepareAmount($row[7] ?? 0);
            $nds = $row[8] ?? '';

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

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id] = [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'amount' => 0,
                    'avans' => 0,
                    'amount_without_nds' => 0,
                    'unwork_avans' => 0,
                    'guarantee' => 0,
                    'guarantee_deadline' => 0,
                    'balance_contract' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

            $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amountDebt;
            $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += $amountDebtWithoutDNS;
            $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id]['guarantee'] += -$guarantee;
            $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id]['guarantee_deadline'] += -$guaranteeDeadline;
            $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id]['avans'] += -$avans;
            $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id]['unwork_avans'] += $neotrabotAvans;
            $importInfo['contractor']['data'][$object->id]['organizations'][$organization->id]['balance_contract'] += -$balanceContract;

            $importInfo['contractor']['data'][$object->id]['total_amount'] += -$amountDebt;
            $importInfo['contractor']['data'][$object->id]['total_amount_without_nds'] += -$amountDebtWithoutDNS;
            $importInfo['contractor']['data'][$object->id]['total_guarantee'] += -$guarantee;
            $importInfo['contractor']['data'][$object->id]['total_guarantee_deadline'] += -$guaranteeDeadline;
            $importInfo['contractor']['data'][$object->id]['total_avans'] += -$avans;
            $importInfo['contractor']['data'][$object->id]['total_unwork_avans'] += $neotrabotAvans;
            $importInfo['contractor']['data'][$object->id]['total_balance_contract'] += -$balanceContract;
        }

        $providerImportData = $importData['Поставщикам'];

        $importInfo['provider']['data'][$object->id] = [
            'object_id' => $object->id,
            'object_name' => $object->name,
            'total_amount' => 0,
            'total_amount_without_nds' => 0,
            'total_amount_fix' => 0,
            'total_amount_float' => 0,
            'organizations' => []
        ];

        unset($providerImportData[0]);
        $objectOrganizationExist = [];

        foreach ($providerImportData as $row) {
            if (empty($row[0])) {
                continue;
            }

            $organizationName = $row[0];
            $inn = $row[1];
            $amountDebt = $this->prepareAmount($row[2] ?? 0);
            $nds = $row[3] ?? '';
            $amountType = $row[4] ?? '';

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

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['provider']['data'][$object->id]['organizations'][$organization->id] = [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'amount' => 0,
                    'amount_fix' => 0,
                    'amount_float' => 0,
                    'amount_without_nds' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

            $importInfo['provider']['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amountDebt;
            $importInfo['provider']['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += -$amountDebtWithoutDNS;

            if (str_contains(mb_strtolower($amountType), 'фикс')) {
                $importInfo['provider']['data'][$object->id]['organizations'][$organization->id]['amount_fix'] += -$amountDebt;
                $importInfo['provider']['data'][$object->id]['total_amount_fix'] += -$amountDebt;
            } else {
                $importInfo['provider']['data'][$object->id]['organizations'][$organization->id]['amount_float'] += -$amountDebt;
                $importInfo['provider']['data'][$object->id]['total_amount_float'] += -$amountDebt;
            }

            $importInfo['provider']['data'][$object->id]['total_amount'] += -$amountDebt;
            $importInfo['provider']['data'][$object->id]['total_amount_without_nds'] += -$amountDebtWithoutDNS;
        }

        $serviceImportData = $importData['За услуги'];

        $importInfo['service']['data'][$object->id] = [
            'object_id' => $object->id,
            'object_name' => $object->name,
            'total_amount' => 0,
            'total_amount_without_nds' => 0,
            'organizations' => []
        ];

        unset($serviceImportData[0]);
        $objectOrganizationExist = [];

        foreach ($serviceImportData as $row) {
            if (empty($row[0])) {
                continue;
            }

            $organizationName = $row[0];
            $inn = $row[1];
            $amountDebt = $this->prepareAmount($row[2] ?? 0);
            $nds = $row[3] ?? '';

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

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['service']['data'][$object->id]['organizations'][$organization->id] = [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'amount' => 0,
                    'amount_without_nds' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

            $importInfo['service']['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amountDebt;
            $importInfo['service']['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += -$amountDebtWithoutDNS;

            $importInfo['service']['data'][$object->id]['total_amount'] += -$amountDebt;
            $importInfo['service']['data'][$object->id]['total_amount_without_nds'] += -$amountDebtWithoutDNS;
        }

        return $importInfo;
    }
}