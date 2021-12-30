<?php

namespace App\Exports\Payment;

use App\Exports\Payment\Sheets\PaymentSheet;
use App\Exports\Payment\Sheets\PivotSheet;
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
        return [
            new PivotSheet('Сводная', $this->payments),
            new PaymentSheet('Таблица оплат', $this->payments, $this->payments->count())
        ];
    }
}
