<?php

namespace App\Services\DebtImport\Imports;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ContractorImportFromSupply as ContractorExcelImport;
use Illuminate\Http\UploadedFile;

class ContractorImportFromSupply extends BaseImport
{
    public function __construct()
    {
        parent::__construct();

        $this->filename = 'supply.xlsx';
    }

    public function import(): array
    {
        $importInfo = [
            'errors' => [],
            'data' => [],
        ];

        if ($this->fileToImportNotExist()) {
            $importInfo['errors'][] = 'Файл для импорта долгов по подрядчикам "' . $this->filename . '" не найден';

            return $importInfo;
        }

        $importData = Excel::toArray(new ContractorExcelImport(), new UploadedFile($this->getLatestFileToImport(), $this->filename));
        $importData = $importData['ПОДРЯДЧИКИ'];

        $notExistObjects = [];
        $objectOrganizationExist = [];

        $activeObjectCodes = BObject::getCodesForContractorImportDebts();

        foreach ($importData as $index =>  $row) {
            if ($index < 3) {
                continue;
            }

            if (empty($row[8] ?? '')) {
                continue;
            }

            $organizationName = trim($row[3] ?? '');
            $objectCode = trim($row[8] ?? '');
            $amount = $row[12] ?? 0;
            $amountWithoutNDS = $amount;
            $type = trim($row[14] ?? '');

            if (empty($amount) || empty($objectCode) || ! is_valid_amount_in_range($amount)) {
                continue;
            }

            if (in_array($objectCode, $notExistObjects)) {
                continue;
            }

            if (str_contains($objectCode, ',')) {
                $mainCode = mb_substr($objectCode, 0, mb_strpos($objectCode, ','));
            } elseif (str_contains($objectCode, '.')) {
                $mainCode = mb_substr($objectCode, 0, mb_strpos($objectCode, '.'));
            } else {
                $mainCode = $objectCode;
            }

            $object = BObject::where('code', $mainCode)->first();

            if (! $object) {
                $importInfo['errors'][] = 'Объекта "' . $objectCode . '" нет в системе ОМС.';
                $notExistObjects[] = $objectCode;

                continue;
            }

            if (in_array($object->code, $activeObjectCodes)) {
                continue;
            }

            $organization = $this->organizationService->getOrCreateOrganization([
                'name' => $organizationName,
                'inn' => null,
                'company_id' => null,
                'kpp' => null
            ]);

            if (! isset($objectOrganizationExist[$object->id])) {
                $importInfo['data'][$object->id] = [
                    'object_id' => $object->id,
                    'object_name' => $object->name,
                    'total_amount' => 0,
                    'total_guarantee' => 0,
                    'total_amount_without_nds' => 0,
                    'organizations' => []
                ];

                $objectOrganizationExist[$object->id] = [];
            }

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['data'][$object->id]['organizations'][$organization->id] = [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'amount' => 0,
                    'guarantee' => 0,
                    'amount_without_nds' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

            if ($type !== 'ГУ') {
                $importInfo['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amount;
                $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += -$amountWithoutNDS;

                $importInfo['data'][$object->id]['total_amount'] += -$amount;
                $importInfo['data'][$object->id]['total_amount_without_nds'] += -$amountWithoutNDS;
            } else {
                $importInfo['data'][$object->id]['organizations'][$organization->id]['guarantee'] += -$amount;
                $importInfo['data'][$object->id]['total_guarantee'] += -$amount;
            }
        }

        return $importInfo;
    }
}