<?php

namespace App\Imports\Bank;

class SberbankImport extends BankImport
{
    public function processImportData(array $importData): array
    {
        $returnData = [];
        $importData = $importData[0];

        $incomingBalance = $importData[count($importData) - 4][4];
        $incomingBalance = str_replace(',', '.', $incomingBalance);

        $outgoingBalance = $importData[count($importData) - 2][4];
        $outgoingBalance = str_replace(',', '.', $outgoingBalance);

        $returnData['incoming_balance'] = (float) preg_replace("/[^-.0-9]/", '', $incomingBalance);
        $returnData['outgoing_balance'] = (float) preg_replace("/[^-.0-9]/", '', $outgoingBalance);

        foreach ($importData as $rowNum => $rowData) {

            if ($rowNum < 4) {
                continue;
            }

            if (in_array('Всего', $rowData)) {
                break;
            }

            $organizationSenderInfo = explode("\n", $rowData[1]);
            $organizationReceiverInfo = explode("\n", $rowData[3]);

            $description = $this->cleanValue($rowData[12]);

            $returnData['payments'][] = [
                'pay_amount' =>  (float) $this->cleanValue((-1) * $rowData[5]),
                'receive_amount' => (float) $this->cleanValue($rowData[6]),
                'organization_sender_inn' => $organizationSenderInfo[1],
                'organization_receiver_inn' => $organizationReceiverInfo[1],
                'organization_name' => $organizationReceiverInfo[2],
                'description' => $description,
                'has_nds' => $this->paymentService->checkHasNDSFromDescription($description)
            ];
        }

        return $returnData;
    }
}
