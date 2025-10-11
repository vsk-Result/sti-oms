<?php

namespace App\Services\DebtImport\Imports;

use App\Models\Object\BObject;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ServiceImportFrom1C as ServiceExcelImport;
use Illuminate\Http\UploadedFile;

class ServiceImportFrom1C extends BaseImport
{
    public function __construct()
    {
        parent::__construct();

        $this->filename = 'Uslugi(XLSX).xlsx';
    }

    public function import(): array
    {
        $importInfo = [
            'errors' => [],
            'data' => [],
        ];

        if ($this->fileToImportNotExist()) {
            $importInfo['errors'][] = 'Файл для импорта долгов по услугам "' . $this->filename . '" не найден';

            return $importInfo;
        }

        $importData = Excel::toArray(new ServiceExcelImport(), new UploadedFile($this->getLatestFileToImport(), $this->filename));
        $importData = $importData['TDSheet'];

        unset($importData[0]);

        $notExistObjects = [];
        $objectOrganizationExist = [];
        foreach ($importData as $row) {
            if (($row[0] ?? '') === 'Итого') {
                continue;
            }

            $organizationName = trim($row[3] ?? '');
            $organizationType = trim($row[4] ?? '');
            $objectCode = trim($row[9] ?? '');
            $amount = $row[13] ?? 0;
            $amountWithoutNDS = $row[21] ?? 0;
            $inn = trim($row[22] ?? '');
            $period = trim($row[23] ?? '');
            $type = trim($row[24] ?? '');

            if ($organizationType !== 'НАКЛАДНЫЕ/УСЛУГИ') {
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
                    'total_avans' => 0,
                    'total_amount' => 0,
                    'total_amount_without_nds' => 0,
                    'organizations' => []
                ];

                $objectOrganizationExist[$object->id] = [];
            }

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['data'][$object->id]['organizations'][$organization->id] = [
                    'organization_id' => $organization->id,
                    'organization_name' => $organization->name,
                    'avans' => 0,
                    'amount' => 0,
                    'amount_without_nds' => 0,
                    'details' => [],
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

            if (! empty($period)) {
                $importInfo['data'][$object->id]['organizations'][$organization->id]['details'][] = [
                    'type' => $type !== 'Аванс' ? 'amount' : 'avans',
                    'date' => Carbon::parse($period)->format('Y-m-d'),
                    'amount' => -$amount
                ];
            }
        }

        return $importInfo;
    }
}