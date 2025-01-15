<?php

namespace App\Services\DebtImport\Imports;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ContractorImportFrom1C as ContractorExcelImport;
use Illuminate\Http\UploadedFile;

class ContractorImportFrom1C extends BaseImport
{
    public function __construct()
    {
        parent::__construct();

        $this->filename = 'Raboty(XLSX).xlsx';
        $this->additionalData = [
            'closed_object_codes' => ['346', '349', '353']
        ];
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
            $inn = trim($row[22] ?? '');
            $type = trim($row[23] ?? '');

            if ($type === 'Гарантийное удержание') {
                continue;
            }

            if ($organizationType !== 'РАБОТЫ') {
                continue;
            }

            if (empty($amount) || empty($objectCode) || ! is_valid_amount_in_range($amount)) {
                continue;
            }

            if (in_array($objectCode, $notExistObjects)) {
                continue;
            }

            if (in_array($objectCode, $this->additionalData['closed_object_codes'])) {
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
                    'total_avans' => 0,
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
                    'avans' => 0,
                    'amount_without_nds' => 0,
                ];

                $objectOrganizationExist[$object->id][$organization->id] = true;
            }

            if ($type !== 'Аванс') {
                $importInfo['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amount;
                $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += -$amountWithoutNDS;

                $importInfo['data'][$object->id]['total_amount'] += -$amount;
                $importInfo['data'][$object->id]['total_amount_without_nds'] += -$amountWithoutNDS;
            } else {
                $importInfo['data'][$object->id]['organizations'][$organization->id]['avans'] += -$amount;
                $importInfo['data'][$object->id]['total_avans'] += -$amount;
            }
        }

        return $importInfo;
    }
}