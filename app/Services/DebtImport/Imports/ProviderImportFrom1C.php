<?php

namespace App\Services\DebtImport\Imports;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ProviderImportFrom1C as ProviderExcelImport;
use Illuminate\Http\UploadedFile;

class ProviderImportFrom1C extends BaseImport
{
    public function __construct()
    {
        parent::__construct();

        $this->filename = 'TabelOMS_(XLSX).xlsx';
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
        $importData = $importData['TDSheet'];

        unset($importData[0]);

        $notExistObjects = [];
        $objectOrganizationExist = [];
        foreach ($importData as $row) {
            if (($row[0] ?? '') === 'Итого') {
                continue;
            }
            $organizationType = trim($row[4] ?? '');
            $organizationName = trim($row[3] ?? '');
            $objectCode = trim($row[9] ?? '');
            $amount = $row[13] ?? 0;
            $amountWithoutNDS = $row[21] ?? 0;
            $inn = trim($row[23] ?? '');
            $amountType = trim($row[22] ?? '');

            if ($organizationType !== 'МАТЕРИАЛЫ') {
                continue;
            }


            if (empty($amount) || empty($objectCode) || ! is_valid_amount_in_range($amount)) {
                continue;
            }

            if (in_array($objectCode, $notExistObjects)) {
                continue;
            }

            $object = BObject::where('code', $objectCode)->first();

            if (! $object) {
                $importInfo['errors'][] = 'Объекта "' . $objectCode . '" нет в системе ОМС.';
                $notExistObjects[] = $objectCode;

                continue;
            }

            $organization = $this->organizationService->getOrCreateOrganization([
                'inn' => $inn,
                'name' => $organizationName,
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
                    'amount_fix' => 0,
                    'amount_float' => 0,
                    'amount_without_nds' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

            $importInfo['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amount;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += -$amountWithoutNDS;

            if ($amountType === 'Фиксированная часть') {
                $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_fix'] += -$amount;
                $importInfo['data'][$object->id]['total_amount_fix'] += -$amount;
            } else {
                $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_float'] += -$amount;
                $importInfo['data'][$object->id]['total_amount_float'] += -$amount;
            }

            $importInfo['data'][$object->id]['total_amount'] += -$amount;
            $importInfo['data'][$object->id]['total_amount_without_nds'] += -$amountWithoutNDS;
        }

        return $importInfo;
    }
}