<?php

namespace App\Exports\Payment;

use App\Exports\Payment\Sheets\BackupSheet;
use App\Exports\Payment\Sheets\KostCodePivot;
use App\Exports\Payment\Sheets\PaymentSheet;
use App\Exports\Payment\Sheets\PivotSheet;
use App\Exports\Payment\Sheets\CategoryPivot;
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
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);

        $object = null;
        $objectName = null;
        $objectIds = array_unique((clone $this->payments)->pluck('object_id')->toArray());
        if (count($objectIds) === 1) {
            $object = BObject::find($objectIds[0]);
            $objectName = $object ? $object->getName() : '';
        }

        if (auth()->id() === 1) {
            return [
                new BackupSheet('История'),
            ];
        }

        return [
            new PivotSheet('Сводная', (clone $this->payments), $objectName),
            new PaymentSheet('Таблица оплат', (clone $this->payments), $this->payments->count()),
            new KostCodePivot('Сводная по статьям затрат', (clone $this->payments), $object),
            new CategoryPivot('Сводная по категориям', (clone $this->payments))
        ];
    }
}
