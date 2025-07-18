<?php

namespace App\Exports\Object\Report\PaymentReceiveReport;

use App\Exports\Object\Report\PaymentReceiveReport\Sheets\PaymentReceiveSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    public function __construct(private array $reportInfo) {}

    public function sheets(): array
    {
        return [
            new PaymentReceiveSheet($this->reportInfo),
        ];
    }
}
