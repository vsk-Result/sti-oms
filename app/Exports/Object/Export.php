<?php

namespace App\Exports\Object;

use App\Exports\Object\Sheets\ActSheet;
use App\Exports\Object\Sheets\PaymentSheet;
use App\Exports\Object\Sheets\DebtsSheet;
use App\Exports\Object\Sheets\DebtsPivotSheet;
use App\Exports\Object\Sheets\GuaranteeSheet;
use App\Exports\Object\Sheets\BankGuaranteeSheet;
use App\Models\BankGuarantee;
use App\Models\Contract\Act;
use App\Models\Guarantee;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private BObject $object;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(BObject $object, PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function sheets(): array
    {
        $paymentsQuery = Payment::where('object_id', $this->object->id)
            ->with('company', 'import', 'createdBy', 'object', 'audits', 'organizationSender', 'organizationReceiver')
            ->orderByDesc('date')
            ->orderByDesc('id');

        $actsQuery = Act::where('object_id', $this->object->id)
            ->with('object', 'contract', 'payments')
            ->orderByDesc('date')
            ->orderByDesc('id');

        $guaranteesQuery = Guarantee::where('object_id', $this->object->id)
            ->with('company', 'object', 'contract', 'customer')
            ->orderByDesc('object_id');

        $bankGuarantees = BankGuarantee::where('object_id', $this->object->id)
            ->with('company', 'object', 'contract', 'contract.acts', 'contract.avansesReceived', 'organization')
            ->orderByDesc('object_id')->get();

        return [
            new PaymentSheet('Оплаты', $paymentsQuery, (clone $paymentsQuery)->count()),
            new PaymentSheet('Касса', (clone $paymentsQuery)->where('payment_type_id', Payment::PAYMENT_TYPE_CASH), (clone $paymentsQuery)->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)->count()),
            new DebtsPivotSheet('Сводная по долгам', $this->object, $this->pivotObjectDebtService),
            new DebtsSheet('Детализация по долгам', $this->object),
            new ActSheet('Акты', $actsQuery, (clone $actsQuery)->count()),
            new GuaranteeSheet('Гарантийные удержания', $guaranteesQuery, (clone $guaranteesQuery)->count()),
            new BankGuaranteeSheet('БГ и депозиты', $bankGuarantees),
        ];
    }
}
