<?php

namespace App\Services\DebtImport\Imports;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ContractorImportFromClosedObject as ContractorExcelImport;
use Illuminate\Http\UploadedFile;

class ContractorImportFromClosedObject extends BaseImport
{
    public function __construct()
    {
        parent::__construct();

        $this->filename = '000.xlsx';
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

        $importData = Excel::toArray(new ContractorExcelImport(), new UploadedFile($this->getLatestFileToImport(), $this->filename));

        $importData = $importData['ГУ закрытые объекты'];

        $object = BObject::where('code', '000')->first();

        $importInfo['data'][$object->id] = [
            'object_id' => $object->id,
            'object_name' => $object->name,
            'total_guarantee' => 0,
            'organizations' => []
        ];

        unset($importData[0]);

        $objectOrganizationExist = [];
        foreach ($importData as $row) {
            if (empty($row[1])) {
                continue;
            }

            $organizationName = trim($row[1] ?? '');
//            $objectCode = trim($row[0] ?? '');
            $guarantee = 0;

            $organization = $this->organizationService->getOrCreateOrganization([
                'inn' => null,
                'name' => $organizationName,
                'company_id' => null,
                'kpp' => null
            ]);

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['data'][$object->id]['organizations'][$organization->id] = [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'guarantee' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

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

            $importInfo['data'][$object->id]['organizations'][$organization->id]['guarantee'] += -$guarantee;
            $importInfo['data'][$object->id]['total_guarantee'] += -$guarantee;
        }

        return $importInfo;
    }
}