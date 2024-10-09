<?php

namespace App\Exports\Pivot\MoneyMovement;

use App\Exports\Pivot\MoneyMovement\Sheets\PaymentSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotObjectSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationReceiveSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationPaymentSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationReceiveWithObjectSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationPaymentWithObjectSheet;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $exceptOrganizations = Organization::whereIn('name', ['Общество с ограниченной ответственностью "БИЗНЕС АКТИВ"'])->pluck('id')->toArray();
        $exceptPaymentIds = Payment::where('category', Payment::CATEGORY_TRANSFER)->where(function($q) use($exceptOrganizations) {
            $q->where('description', 'LIKE', '%перевод собственных денежных средств%');
            $q->orWhere('description', 'LIKE', '%перевод собственных средств%');
            $q->orWhere('description', 'LIKE', '%депозит%');
            $q->orWhereIn('organization_receiver_id', $exceptOrganizations);
            $q->orWhereIn('organization_sender_id', $exceptOrganizations);
        })->pluck('id')->toArray();

        $this->payments = $payments->whereNotIn('id', $exceptPaymentIds)->where('company_id', Company::getSTI()->id);
    }

    public function sheets(): array
    {
        return [
            new PivotObjectSheet((clone $this->payments)),
            new PaymentSheet((clone $this->payments)),
            new PivotOrganizationReceiveSheet((clone $this->payments)),
            new PivotOrganizationPaymentSheet((clone $this->payments)),
            new PivotOrganizationReceiveWithObjectSheet((clone $this->payments)),
            new PivotOrganizationPaymentWithObjectSheet((clone $this->payments)),
        ];
    }
}
