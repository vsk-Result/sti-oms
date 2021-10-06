<?php

namespace App\Imports\Bank;

class SovkombankImport extends BankImport
{
    public function processImportData(array $importData): array
    {
        $returnData = [];
        $importData = $importData[0];

        $incomingBalance = $importData[count($importData) - 3][5];
        $incomingBalance = str_replace(',', '.', $incomingBalance);

        $outgoingBalance = $importData[count($importData) - 1][5];
        $outgoingBalance = str_replace(',', '.', $outgoingBalance);

        $returnData['incoming_balance'] = (float) preg_replace("/[^-.0-9]/", '', $incomingBalance);
        $returnData['outgoing_balance'] = (float) preg_replace("/[^-.0-9]/", '', $outgoingBalance);

        foreach ($importData as $rowNum => $rowData) {

            if ($rowNum < 5) {
                continue;
            }

            if (in_array('Всего', $rowData)) {
                break;
            }

            $organizationSenderInfo = explode("\n", $rowData[2]);
            $organizationReceiverInfo = explode("\n", $rowData[4]);

            $description = $this->cleanValue($rowData[16]);
            if ($rowData[12] === 'Д') {
                $payAmount = (float) $this->cleanValue((-1) * $rowData[9]);
                $receiveAmount = 0;
                $organizationName = $organizationReceiverInfo[2];
            } else {
                $payAmount = 0;
                $receiveAmount = (float) $this->cleanValue($rowData[9]);
                $organizationName = $organizationSenderInfo[2];
            }

            $returnData['payments'][] = [
                'pay_amount' => $payAmount,
                'receive_amount' => $receiveAmount,
                'organization_sender_inn' => $organizationSenderInfo[1],
                'organization_receiver_inn' => $organizationReceiverInfo[1],
                'organization_name' => $organizationName,
                'description' => $description,
                'has_nds' => $this->paymentService->checkHasNDSFromDescription($description)
            ];
        }

        return $returnData;
    }
}
