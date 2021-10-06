<?php

namespace App\Imports\Bank;

class VTBImport extends BankImport
{
    public function processImportData(array $importData): array
    {
        $returnData = [];
        $importData = $importData[0];

        $incomingBalance = $importData[1][0];
        $outgoingBalance = $importData[count($importData) - 3][0];
        if (str_contains($incomingBalance, 'RUR')) {
            $incomingBalance = substr(
                $incomingBalance,
                strpos($incomingBalance, ':') + 1,
                strpos($incomingBalance, 'RUR') - 1
            );
            $outgoingBalance = substr(
                $outgoingBalance,
                strpos($outgoingBalance, ':') + 1,
                strpos($outgoingBalance, 'RUR') - 1
            );
        } else {
            $incomingBalance = $importData[1][5];
            $outgoingBalance = $importData[count($importData) - 3][13];
        }

        $returnData['incoming_balance'] = (float) preg_replace("/[^-.0-9]/", '', $incomingBalance);
        $returnData['outgoing_balance'] = (float) preg_replace("/[^-.0-9]/", '', $outgoingBalance);

        foreach ($importData as $rowNum => $rowData) {

            if ($rowNum < 4) {
                continue;
            }

            if (in_array('Итого', $rowData)) {
                break;
            }

            if (count($rowData) === 20) {
                $payAmount = (-1) * $rowData[14];
                $receiveAmount = $rowData[17];
                $organizationSenderInn = $rowData[2];
                $organizationReceiverInn = $rowData[3];
                if (empty($rowData[8])) {
                    $organizationName = $rowData[7];
                } else {
                    if (empty($rowData[9])) {
                        $organizationName = $rowData[7] . ' ' . $rowData[8];
                    } else {
                        $explode = explode("\n", $rowData[8]);
                        $organizationName = $rowData[7] . ' ' . $explode[0] . ' ' . $rowData[9];
                        array_shift($explode);
                        $organizationName .= ' ' . implode("\n", $explode);
                    }
                }
                $description = $rowData[11];
            } else {
                $payAmount = (-1) * $rowData[10];
                $receiveAmount = $rowData[11];
                $organizationSenderInn = $rowData[2];
                $organizationReceiverInn = $rowData[3];
                $organizationName = $rowData[7];
                $description = $rowData[9];
            }

            $description = $this->cleanValue($description);
            $organizationName = $this->cleanValue($organizationName);

            if (str_contains($organizationName, 'НДС полученный')) {
                $organizationName = 'Филиал "Центральный" Банка ВТБ (ПАО)';
            }

            $returnData['payments'][] = [
                'pay_amount' =>  (float) $this->cleanValue($payAmount),
                'receive_amount' => (float) $this->cleanValue($receiveAmount),
                'organization_sender_inn' => $this->cleanValue($organizationSenderInn),
                'organization_receiver_inn' => $this->cleanValue($organizationReceiverInn),
                'organization_name' => $organizationName,
                'description' => $description,
                'has_nds' => $this->paymentService->checkHasNDSFromDescription($description)
            ];
        }

        return $returnData;
    }
}
