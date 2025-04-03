<?php

namespace App\Exports\Object\ReceivePlan;

use App\Exports\Object\ReceivePlan\Sheets\PaymentPlanSheet;
use App\Exports\Object\ReceivePlan\Sheets\ReceivePlanSheet;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $reasons;
    private array $periods;
    private array $payments;
    private Collection $plans;

    public function __construct(
        array $reasons,
        array $periods,
        Collection $plans,
        array $payments,
    ) {
        $this->reasons = $reasons;
        $this->periods = $periods;
        $this->plans = $plans;
        $this->payments = $payments;
    }

    public function sheets(): array
    {
        return [
            new ReceivePlanSheet(
                $this->reasons,
                $this->periods,
                $this->plans,
            ),
            new PaymentPlanSheet( $this->periods, $this->payments)
        ];
    }
}
