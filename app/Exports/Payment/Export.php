<?php

namespace App\Exports\Payment;

use App\Exports\Payment\Sheets\PaymentSheet;
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
        $sheets = [];

        $sheets[] = new PaymentSheet('Таблица оплат', $this->payments);

        return $sheets;
    }
}
