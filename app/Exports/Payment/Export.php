<?php

namespace App\Exports\Payment;

use App\Exports\Payment\Sheets\KostCodePivot;
use App\Exports\Payment\Sheets\PaymentSheet;
use App\Exports\Payment\Sheets\PivotSheet;
use App\Models\Object\BObject;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $this->payments = $payments;
    }

    public function sheets(): array
    {

        $object = null;
        $objectIds = (clone $this->payments)->pluck('object_id')->unique('object_id');
        if (count($objectIds) === 1) {
            $object = BObject::find($objectIds[0]);
            $object = $object ? $object->getName() : '';
        }
        return [
            new PivotSheet('Сводная', (clone $this->payments), $object),
            new PaymentSheet('Таблица оплат', (clone $this->payments), $this->payments->count()),
//            new KostCodePivot('Сводная по статьям затрат', (clone $this->payments))
        ];
    }
}
