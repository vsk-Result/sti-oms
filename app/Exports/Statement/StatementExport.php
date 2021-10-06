<?php

namespace App\Exports\Statement;

use App\Exports\Statement\Sheets\PaymentObjectSheet;
use App\Exports\Statement\Sheets\PaymentTransferSheet;
use App\Exports\Statement\Sheets\PaymentGeneralSheet;
use App\Models\Statement;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StatementExport implements WithMultipleSheets
{
    private Statement $statement;

    public function __construct(Statement $statement)
    {
        $this->statement = $statement->load(
            'payments',
            'payments.object',
            'payments.company',
            'payments.organizationReceiver',
            'payments.organizationSender'
        );
    }

    public function sheets(): array
    {
        $sheets = [];
        $groupedByTypePayments = $this->statement->payments->groupBy('type');

        foreach ($groupedByTypePayments as $type => $gPayments) {
            if (empty($type)) {
                $sheets[] = new PaymentObjectSheet('Без кода', $gPayments);
            } elseif ($type === 'object') {
                $groupedPayments = $gPayments->groupBy('object_id');
                foreach ($groupedPayments as $objectId => $payments) {
                    $sheets[] = new PaymentObjectSheet(BObject::find($objectId)->code, $payments);
                }
            } elseif ($type === 'general') {
                $sheets[] = new PaymentGeneralSheet($gPayments);
            } elseif ($type === 'transfer') {
                $sheets[] = new PaymentTransferSheet($gPayments);
            }
        }

        return $sheets;
    }
}
