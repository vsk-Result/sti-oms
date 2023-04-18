<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Guarantee;
use App\Models\GuaranteePayment;
use App\Models\Status;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Currency;

class GuaranteeService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function filterGuarantee(array $requestData, array &$total = [], bool $needPaginate = true): LengthAwarePaginator|Collection
    {
        $query = Guarantee::query();

        if (! empty($requestData['currency'])) {
            $query->whereIn('currency', $requestData['currency']);
        }

        if (! empty($requestData['contract_id'])) {
            $query->whereIn('contract_id', $requestData['contract_id']);
        }

        if (! empty($requestData['object_id'])) {
            $query->whereIn('object_id', $requestData['object_id']);
        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $total['amount']['RUB'] = (clone $query)->where('currency', 'RUB')->sum('amount');
        $total['amount']['EUR'] = (clone $query)->where('currency', 'EUR')->sum('amount');

        $total['fact_amount']['RUB'] = (clone $query)->where('currency', 'RUB')->sum('fact_amount') - (clone $query)->where('currency', 'RUB')->sum('amount_payments');
        $total['fact_amount']['EUR'] = (clone $query)->where('currency', 'EUR')->sum('fact_amount') - (clone $query)->where('currency', 'EUR')->sum('amount_payments');

        $query->with('company', 'object', 'contract', 'customer');
        $query->orderByDesc('object_id');

        return $needPaginate ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function createGuarantee(array $requestData): Guarantee
    {
        $guarantee = Guarantee::create([
            'contract_id' => $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'object_id' => $requestData['object_id'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'fact_amount' => $this->sanitizer->set($requestData['fact_amount'])->toAmount()->get(),
            'state' => $this->sanitizer->set($requestData['state'])->get(),
            'conditions' => $this->sanitizer->set($requestData['conditions'])->get(),
            'has_bank_guarantee' => $requestData['has_bank_guarantee'],
            'has_final_act' => $requestData['has_final_act'],
            'currency' => $requestData['currency'] ?? 'RUB',
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        if (! empty($requestData['payments_date'])) {
            foreach ($requestData['payments_date'] as $index => $paymentDate) {
                $paymentAmount = $this->sanitizer->set($requestData['payments_amount'][$index])->toAmount()->get();
                if ($paymentAmount > 0) {
                    GuaranteePayment::create([
                        'contract_id' => $requestData['contract_id'],
                        'guarantee_id' => $guarantee->id,
                        'company_id' => $requestData['company_id'],
                        'object_id' => $requestData['object_id'],
                        'date' => $paymentDate,
                        'amount' => $this->sanitizer->set($paymentAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $guarantee->currency,
                    ]);
                }
            }
        }

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $guarantee->addMedia($file)->toMediaCollection();
            }
        }

        $guarantee->updatePayments();

        return $guarantee;
    }

    public function updateGuarantee(Guarantee $guarantee, array $requestData): Guarantee
    {
        $guarantee->update([
            'contract_id' => $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'object_id' => $requestData['object_id'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'fact_amount' => $this->sanitizer->set($requestData['fact_amount'])->toAmount()->get(),
            'state' => $this->sanitizer->set($requestData['state'])->get(),
            'conditions' => $this->sanitizer->set($requestData['conditions'])->get(),
            'has_bank_guarantee' => $requestData['has_bank_guarantee'],
            'has_final_act' => $requestData['has_final_act'],
            'currency' => $requestData['currency'] ?? 'RUB',
            'status_id' => $requestData['status_id'],
        ]);

        $currentPaymentsIds = $guarantee->payments()->pluck('id', 'id')->toArray();

        if (! empty($requestData['isset_payments_date'])) {
            foreach ($requestData['isset_payments_date'] as $paymentId => $paymentDate) {
                $payment = GuaranteePayment::find($paymentId);
                $paymentAmount = $this->sanitizer->set($requestData['isset_payments_amount'][$paymentId])->toAmount()->get();

                $payment->update([
                    'date' => $paymentDate,
                    'amount' => $paymentAmount,
                    'currency' => $guarantee->currency,
                ]);

                unset($currentPaymentsIds[$paymentId]);
            }
        }

        foreach ($currentPaymentsIds as $paymentId) {
            $payment = GuaranteePayment::find($paymentId);
            $payment->delete();
        }

        if (! empty($requestData['payments_date'])) {
            foreach ($requestData['payments_date'] as $index => $paymentDate) {
                $paymentAmount = $this->sanitizer->set($requestData['payments_amount'][$index])->toAmount()->get();

                if ($paymentAmount > 0) {
                    GuaranteePayment::create([
                        'contract_id' => $guarantee->contract_id,
                        'guarantee_id' => $guarantee->id,
                        'company_id' => $guarantee->company_id,
                        'object_id' => $guarantee->object_id,
                        'date' => $paymentDate,
                        'amount' => $paymentAmount,
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $guarantee->currency,
                    ]);
                }
            }
        }

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $guarantee->addMedia($file)->toMediaCollection();
            }
        }

        $guarantee->updatePayments();

        return $guarantee;
    }

    public function destroyGuarantee(Guarantee $guarantee): void
    {
        $guarantee->payments()->delete();
        $guarantee->delete();
    }
}
