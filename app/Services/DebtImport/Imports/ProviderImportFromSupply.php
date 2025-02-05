<?php

namespace App\Services\DebtImport\Imports;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ProviderImportFromSupply as ProviderExcelImport;
use Illuminate\Http\UploadedFile;

class ProviderImportFromSupply extends BaseImport
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
            $importInfo['errors'][] = 'Файл для импорта долгов по поставщикам "' . $this->filename . '" не найден';

            return $importInfo;
        }

        $importData = Excel::toArray(new ProviderExcelImport(), new UploadedFile($this->getLatestFileToImport(), $this->filename));
        $importData = $importData['МАТЕРИАЛЫ'];

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

            if ($mainCode == '27') {
                continue;
            }

            $object = BObject::where('code', $mainCode)->first();

            if (! $object) {
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
                    'total_amount_without_nds' => 0,
                    'total_amount_fix' => 0,
                    'total_amount_float' => 0,
                    'organizations' => []
                ];

                $objectOrganizationExist[$object->id] = [];
            }

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['data'][$object->id]['organizations'][$organization->id] = [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'amount' => 0,
                    'amount_without_nds' => 0,
                    'amount_fix' => 0,
                    'amount_float' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

            $importInfo['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amount;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += -$amountWithoutNDS;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_fix'] += -$amount;

            $importInfo['data'][$object->id]['total_amount'] += -$amount;
            $importInfo['data'][$object->id]['total_amount_without_nds'] += -$amountWithoutNDS;
            $importInfo['data'][$object->id]['total_amount_fix'] += -$amount;
        }

        return $importInfo;
    }
}