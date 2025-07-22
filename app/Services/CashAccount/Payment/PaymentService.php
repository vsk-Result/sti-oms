<?php

namespace App\Services\CashAccount\Payment;

use App\Helpers\Sanitizer;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\Object\BObject;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService
{
    public function __construct(private Sanitizer $sanitizer) {}

    public function filterPayments(array $requestData, bool $needPaginate = false, array &$totalInfo = []): Builder|LengthAwarePaginator
    {
        $paymentQuery = CashAccountPayment::query();

        if (! empty($requestData['cash_account_id'])) {
            $paymentQuery->whereIn('cash_account_id', $requestData['cash_account_id']);
        }

        if (! empty($requestData['sort_by'])) {
            if ($requestData['sort_by'] == 'organization_id') {
                $paymentQuery->orderBy(Organization::select('name')->whereColumn('organizations.id', 'payments.organization_receiver_id')->orWhereColumn('organizations.id', 'payments.organization_sender_id'), $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'object_id') {
                $paymentQuery->orderBy('type_id', $requestData['sort_direction'] ?? 'asc');
                $paymentQuery->orderBy(BObject::select('code')->whereColumn('objects.id', 'payments.object_id'), $requestData['sort_direction'] ?? 'asc');
                $paymentQuery->orderBy('object_worktype_id', $requestData['sort_direction'] ?? 'asc');
            } else {
                $paymentQuery->orderBy($requestData['sort_by'], $requestData['sort_direction'] ?? 'asc');
            }
        } else {
            $paymentQuery->orderByDesc('date')
                ->orderByDesc('id');
        }

//        $paymentQuery->with('company', 'import', 'createdBy', 'object', 'audits', 'organizationSender', 'organizationReceiver');

        if ($needPaginate) {
            $perPage = 30;

            if (! empty($requestData['count_per_page'])) {
                $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
            }

            $totalInfo['amount_pay'] = (clone $paymentQuery)->where('amount', '<', 0)->sum('amount');
            $totalInfo['amount_receive'] = (clone $paymentQuery)->where('amount', '>=', 0)->sum('amount');

            return $paymentQuery->paginate($perPage)->withQueryString();
        }

        return $paymentQuery;
    }

    public function createPayment(array $requestData): CashAccountPayment
    {
        $paymentCurrency = 'RUB';
        $paymentCurrencyRate = 1;

        $amount = $this->sanitizer->set($requestData['amount'])->toAmount()->get();

        $payment = CashAccountPayment::create([
            'cash_account_id' => $requestData['cash_account_id'],
            'object_id' => $requestData['object_id'],
            'organization_id' => $requestData['organization_id'],
            'type_id' => $requestData['type_id'] ?? CashAccountPayment::TYPE_OBJECT,
            'category' => $requestData['category'],
            'code' => $requestData['code'],
            'description' => $this->sanitizer->set($requestData['description'] ?? '')->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $amount,
            'status_id' => $requestData['status_id'] ?? CashAccountPayment::STATUS_ACTIVE,
            'currency' => $paymentCurrency,
            'currency_rate' => $paymentCurrencyRate,
            'currency_amount' => $amount,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                if (! is_null($file)) {
                    $payment->addMedia($file)->toMediaCollection();
                }
            }
        }

        return $payment;
    }

    public function updatePayment(CashAccountPayment $payment, array $requestData): void
    {
        $amount = $this->sanitizer->set($requestData['amount'])->toAmount()->get();

        $payment->update([
            'object_id' => $requestData['object_id'],
            'organization_id' => $requestData['organization_id'],
            'type_id' => $requestData['type_id'] ?? CashAccountPayment::TYPE_OBJECT,
            'category' => $requestData['category'],
            'code' => $requestData['code'],
            'description' => $this->sanitizer->set($requestData['description'] ?? '')->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $amount,
            'status_id' => $requestData['status_id'] ?? $payment->status_id,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                if (! is_null($file)) {
                    $payment->addMedia($file)->toMediaCollection();
                }
            }
        }
    }

    public function destroyPayment(CashAccountPayment $payment): void
    {
        $payment->delete();
    }
}
