<?php

namespace App\Exports\Payment;

use App\Exports\PaymentImport\Sheets\ObjectSheet;
use App\Exports\PaymentImport\Sheets\TransferSheet;
use App\Exports\PaymentImport\Sheets\GeneralSheet;
use App\Models\Payment;
use App\Models\Object\BObject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private Collection $payments;

    public function __construct(Collection $payments)
    {
        $this->payments = $payments;
    }

    public function sheets(): array
    {
        $sheets = [];
        $groupedByTypePayments = $this->payments->sortBy('type_id')->groupBy('type_id');

        foreach ($groupedByTypePayments as $type => $gPayments) {
            switch ($type) {
                case Payment::TYPE_NONE:
                    $sheets[] = new ObjectSheet('Без кода', $gPayments);
                    break;
                case Payment::TYPE_OBJECT:
                    $groupedPayments = $gPayments->sortBy('object_id')->groupBy('object_id');
                    foreach ($groupedPayments as $objectId => $payments) {
                        $sheets[] = new ObjectSheet(BObject::find($objectId)->code, $payments);
                    }
                    break;
                case Payment::TYPE_GENERAL:
                    $sheets[] = new GeneralSheet($gPayments);
                    break;
                case Payment::TYPE_TRANSFER:
                    $sheets[] = new TransferSheet($gPayments);
                    break;
                default:
                    break;
            }
        }

        $payments = $this->payments->sortBy('type_id')->sortBy('object_id');
        $sheets[] = new ObjectSheet('Общая таблица', $payments);

        return $sheets;
    }
}
