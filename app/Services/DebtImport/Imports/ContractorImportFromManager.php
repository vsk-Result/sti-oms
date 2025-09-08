<?php

namespace App\Services\DebtImport\Imports;

use App\Helpers\Sanitizer;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DebtImport\ContractorImportFromManager as ContractorExcelImport;
use Illuminate\Http\UploadedFile;

class ContractorImportFromManager extends BaseImport
{
    public function __construct(string $filename, string $objectCode)
    {
        parent::__construct();

        $this->filename = $filename;
        $this->objectCode = $objectCode;
        $this->sanitizer = new Sanitizer();
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
//            $importInfo['errors'][] = 'Файл для импорта долгов по подрядчикам "' . $this->filename . '" не найден';

            return $importInfo;
        }

        $importData = Excel::toArray(new ContractorExcelImport(), new UploadedFile($this->getLatestFileToImport(), $this->filename));

        if (! isset($importData['OMS'])) {
            $importInfo['errors'][] = 'В загружаемом файле "' . $this->filename . '" отсутствует лист OMS';

            return $importInfo;
        }

        $importData = $importData['OMS'];

        $objectOrganizationExist = [];

        $object = BObject::where('code', $this->objectCode)->first();

        $importInfo['data'][$object->id] = [
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

        $organizationIndex = null;
        $amountIndex = null;
        $guaranteeIndex = null;
        $neotrabotAvansIndex = null;
        $avansIndex = null;
        $ndsIndex = null;
        $balanceContractIndex = null;
        $innIndex = null;
        $guaranteeDeadlineIndex = null;

        foreach ($importData as $index => $row) {
            if ($index === 0) {
                $organizationIndex = $this->getTitleIndex('Контрагент', $row);
                $amountIndex = $this->getTitleIndex('смр', $row);
                $guaranteeIndex = $this->getTitleIndex('Гарантийное удержание', $row);
                $neotrabotAvansIndex = $this->getTitleIndex('Неотработанный аванс', $row);
                $avansIndex = $this->getTitleIndex('Авансы к оплате', $row);
                $ndsIndex = $this->getTitleIndex('ндс', $row);
                $balanceContractIndex = $this->getTitleIndex('Остаток', $row);
                $innIndex = $this->getTitleIndex('ИНН', $row);
                $guaranteeDeadlineIndex = $this->getTitleIndex('срок наступил', $row);

                continue;
            }

            if (is_null($organizationIndex)) {
                $importInfo['errors'][] = 'В загружаемом файле "' . $this->filename . '" что-то не так с заголовками столбцов';

                return $importInfo;
            }

            if (empty($row[0]) || $row[0] === 'Контрагент' || $row[0] === 'Общий итог' || $row[0] === 'Итого') {
                continue;
            }

            $organizationName = $this->sanitizer->set($row[$organizationIndex])->get();
            $amountDebt = is_null($amountIndex) ? 0 : $row[$amountIndex];
            $guarantee = is_null($guaranteeIndex) ? 0 : $row[$guaranteeIndex];
            $avans = is_null($avansIndex) ? 0 : $row[$avansIndex];
            $nds = is_null($ndsIndex) ? '' : mb_strtolower($row[$ndsIndex]);
            $balanceContract = is_null($balanceContractIndex) ? 0 : $row[$balanceContractIndex];
            $neotrabotAvans =  is_null($neotrabotAvansIndex) ? 0 : $row[$neotrabotAvansIndex];
            $inn = is_null($innIndex) ? '' : trim($row[$innIndex]);
            $guaranteeDeadline = is_null($guaranteeDeadlineIndex) ? 0 : $row[$guaranteeDeadlineIndex];

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

            if (! in_array($object->code, $this->additionalData['closed_object_codes'])) {
                $amountDebt = 0;
                $amountDebtWithoutDNS = 0;
                $avans = 0;
            }

            $organization = $this->organizationService->getOrCreateOrganization([
                'inn' => $inn,
                'name' => $organizationName,
                'company_id' => null,
                'kpp' => null
            ]);

            if (! isset($objectOrganizationExist[$object->id][$organization->id])) {
                $importInfo['data'][$object->id]['organizations'][$organization->id] = [
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

            $importInfo['data'][$object->id]['organizations'][$organization->id]['amount'] += -$amountDebt;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['amount_without_nds'] += $amountDebtWithoutDNS;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['guarantee'] += -$guarantee;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['guarantee_deadline'] += -$guaranteeDeadline;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['avans'] += -$avans;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['unwork_avans'] += $neotrabotAvans;
            $importInfo['data'][$object->id]['organizations'][$organization->id]['balance_contract'] += -$balanceContract;

            $importInfo['data'][$object->id]['total_amount'] += -$amountDebt;
            $importInfo['data'][$object->id]['total_amount_without_nds'] += -$amountDebtWithoutDNS;
            $importInfo['data'][$object->id]['total_guarantee'] += -$guarantee;
            $importInfo['data'][$object->id]['total_guarantee_deadline'] += -$guaranteeDeadline;
            $importInfo['data'][$object->id]['total_avans'] += -$avans;
            $importInfo['data'][$object->id]['total_unwork_avans'] += $neotrabotAvans;
            $importInfo['data'][$object->id]['total_balance_contract'] += -$balanceContract;
        }

        return $importInfo;
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