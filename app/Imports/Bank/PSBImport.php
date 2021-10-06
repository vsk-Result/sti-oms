<?php

namespace App\Imports\Bank;

class PSBImport extends BankImport
{
    public function processImportData(array $importData): array
    {
        $returnData = [];
        $importData = $importData[0];

        $incomingBalance = $importData[2][0];
        $incomingBalance = substr($incomingBalance, strpos($incomingBalance, ':') + 1);
        $incomingBalance = str_replace(',', '.', $incomingBalance);

        $outgoingBalance = $importData[count($importData) - 3][0];
        $outgoingBalance = substr($outgoingBalance, strpos($outgoingBalance, 'кредит:') + 7);
        $outgoingBalance = str_replace(',', '.', $outgoingBalance);

        $returnData['incoming_balance'] = (float) preg_replace("/[^-.0-9]/", '', $incomingBalance);
        $returnData['outgoing_balance'] = (float) preg_replace("/[^-.0-9]/", '', $outgoingBalance);

        foreach ($importData as $rowNum => $rowData) {

            if ($rowNum < 3 || ! is_numeric($rowData[0])) {
                continue;
            }

            $description = $this->cleanValue($rowData[8]);

            $returnData['payments'][] = [
                'pay_amount' => (float)$this->cleanValue((-1) * $rowData[6]),
                'receive_amount' => (float)$this->cleanValue($rowData[7]),
                'organization_sender_inn' => $this->cleanValue($rowData[10]),
                'organization_receiver_inn' => $this->cleanValue($rowData[10]),
                'organization_name' => $this->cleanValue($rowData[9]),
                'description' => $description,
                'has_nds' => $this->paymentService->checkHasNDSFromDescription($description)
            ];
        }

        return $returnData;
    }
}
