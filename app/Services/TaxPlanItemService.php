<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\BankGuarantee;
use App\Models\Currency;
use App\Models\Status;
use App\Models\TaxPlanItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxPlanItemService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function filterTaxPlan(array $requestData, array &$total = [], bool $needPaginate = true): LengthAwarePaginator|Collection
    {
        $query = TaxPlanItem::query();

        if (! empty($requestData['due_date'])) {
            $period = explode(' - ', $requestData['due_date']);
            $query->whereBetween('due_date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['payment_date'])) {
            $period = explode(' - ', $requestData['payment_date']);
            $query->whereBetween('payment_date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['name'])) {
            $query->whereIn('name', $requestData['name']);
        }

        if (! empty($requestData['period'])) {
            $query->where('period', 'LIKE', '%' . $requestData['period'] . '%');
        }

        if (! empty($requestData['object_id'])) {
            $query->whereIn('object_id', $requestData['object_id']);
        }

        if (! empty($requestData['company_id'])) {
            $query->whereIn('company_id', $requestData['company_id']);
        }

        if (! empty($requestData['paid'])) {
            $query->whereIn('paid', $requestData['paid']);
        } else {
            $query->where('paid', false);
        }

//        if (! empty($requestData['filter'])) {
//            if ($requestData['filter'] !== 'all') {
//                if ($requestData['filter'] === 'current') {
//                    $period = [Carbon::now()->subMonthNoOverflow(), Carbon::now()->addMonthNoOverflow()];
//                    $query->where(function($q) use ($period) {
//                        $q->whereBetween('due_date', $period);
//                        $q->orWhere(function($qq) {
//                            $qq->where('due_date', '<=', Carbon::now()->subMonthNoOverflow());
//                            $qq->where('paid', false);
//                        });
//                    });
//                    $query->where('paid', false);
//                } else if ($requestData['filter'] == '2025') {
//                    $period = ['2025-01-01', '2025-12-31'];
//                    $query->whereBetween('due_date', $period);
//                } else if ($requestData['filter'] == '2024') {
//                    $period = ['2024-01-01', '2024-12-31'];
//                    $query->whereBetween('due_date', $period);
//                } else if ($requestData['filter'] == '2023') {
//                    $period = ['2023-01-01', '2023-12-31'];
//                    $query->whereBetween('due_date', $period);
//                }
//            }
//        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        if (! empty($requestData['filter'])) {
            if ($requestData['filter'] === 'current') {
                $query->orderByRaw('ISNULL(due_date), paid ASC')->orderByRaw('due_date ASC');
            } else {
                $query->orderByRaw('ISNULL(due_date), due_date ASC');
            }
        } else {
            $query->orderByRaw('ISNULL(due_date), due_date ASC');
        }

        $total['not_paid'] = (clone $query)->where('paid', false)->sum('amount');

        $query->with('object');

        return $needPaginate ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function createItem(array $requestData): void
    {
        $item = TaxPlanItem::create([
            'name' => $requestData['name'],
            'cash_flow_group_id' => $requestData['cash_flow_group_id'],
            'cash_flow_name' => $requestData['cash_flow_name'],
            'to_cash_flow' => $requestData['to_cash_flow'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'due_date' => $requestData['due_date'],
            'period' => $requestData['period'],
            'in_one_c' => 0,
            'company_id' => $requestData['company_id'],
            'object_id' => $requestData['object_id'],
            'paid' => $requestData['paid'],
            'payment_date' => $requestData['payment_date'],
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function updateItem(TaxPlanItem $item, array $requestData): void
    {
        $item->update([
            'name' => $requestData['name'],
            'cash_flow_group_id' => $requestData['cash_flow_group_id'],
            'cash_flow_name' => $requestData['cash_flow_name'],
            'to_cash_flow' => $requestData['to_cash_flow'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'due_date' => $requestData['due_date'],
            'period' => $requestData['period'],
            'in_one_c' => 0,
            'company_id' => $requestData['company_id'],
            'object_id' => $requestData['object_id'],
            'paid' => $requestData['paid'],
            'payment_date' => $requestData['payment_date'],
            'status_id' => $requestData['status_id'],
        ]);
    }

    public function destroyItem(TaxPlanItem $item): void
    {
        $item->delete();
    }

    public function getPaymentsForCashFlow(): array
    {
        $result = [];

        $payments = TaxPlanItem::where('company_id', 1)
            ->where('paid', false)
            ->where('to_cash_flow', true)
            ->get();

        foreach ($payments as $payment) {
            $result[] = [
                'group_id' => $payment->cash_flow_group_id,
                'object_id' => $payment->object_id,
                'date' => $payment->due_date,
                'name' => empty($payment->cash_flow_name) ? $payment->name : $payment->cash_flow_name,
                'amount' => -abs($payment->amount)
            ];
        }

        return $result;
    }
}
