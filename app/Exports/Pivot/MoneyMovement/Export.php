<?php

namespace App\Exports\Pivot\MoneyMovement;

use App\Exports\Pivot\MoneyMovement\Sheets\DetailedPivotObjectSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PaymentSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotObjectSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationReceiveSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationPaymentSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationReceiveWithObjectSheet;
use App\Exports\Pivot\MoneyMovement\Sheets\PivotOrganizationPaymentWithObjectSheet;
use App\Helpers\Sanitizer;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Payment;
use App\Services\CurrencyExchangeRateService;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private Builder $payments;

    private bool $needGroupByObjects;

    public function __construct(array $filterData)
    {
        $this->needGroupByObjects = $filterData['need_group_by_objects'] ?? false;
        $needTransfers = $filterData['need_transfers'] ?? false;
        $paymentService = new PaymentService(new Sanitizer(), new OrganizationService(new Sanitizer()), new CurrencyExchangeRateService());

        $payments = $paymentService->filterPayments($filterData);

        $exceptOrganizations = Organization::whereIn('name', ['Общество с ограниченной ответственностью "БИЗНЕС АКТИВ"'])->pluck('id')->toArray();
        $exceptPaymentIds = Payment::where('type_id', Payment::TYPE_TRANSFER)->where(function($q) use($exceptOrganizations, $needTransfers) {

            $q->where('description', 'LIKE', '%депозит%');
            $q->orWhere('description', 'LIKE', '%для зачисления заработной платы%');
            $q->orWhere('description', 'LIKE', '%на выплату зарплаты%');
            $q->orWhere('description', 'LIKE', '%на выплату аванса%');

            if (! $needTransfers) {
                $q->orWhere('description', 'LIKE', '%перевод собственных денежных средств%');
                $q->orWhere('description', 'LIKE', '%перевод собственных средств%');
            }

            $q->orWhereIn('organization_receiver_id', $exceptOrganizations);
            $q->orWhereIn('organization_sender_id', $exceptOrganizations);
        })->pluck('id')->toArray();

        $this->payments = $payments->whereNotIn('id', $exceptPaymentIds)->where('company_id', Company::getSTI()->id);
    }

    public function sheets(): array
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);

        $baseSheets = [
            new DetailedPivotObjectSheet((clone $this->payments)),
//            new PivotObjectSheet((clone $this->payments)),
//            new PaymentSheet((clone $this->payments)),
        ];

        $additionalSheets = $this->needGroupByObjects
            ? [
//                new PivotOrganizationReceiveWithObjectSheet((clone $this->payments)),
//                new PivotOrganizationPaymentWithObjectSheet((clone $this->payments)),
            ]
            : [
//                new PivotOrganizationReceiveSheet((clone $this->payments)),
//                new PivotOrganizationPaymentSheet((clone $this->payments)),

            ];

        return array_merge($baseSheets, $additionalSheets);
    }
}
