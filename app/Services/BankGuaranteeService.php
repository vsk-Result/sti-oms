<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\BankGuarantee;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class BankGuaranteeService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function filterBankGuarantee(array $requestData, array &$total): LengthAwarePaginator
    {
        $query = BankGuarantee::query();

        if (! empty($requestData['bg_period'])) {
            $period = explode(' - ', $requestData['bg_period']);
            $query->whereBetween('start_date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['deposit_period'])) {
            $period = explode(' - ', $requestData['deposit_period']);
            $query->whereBetween('start_date_deposit', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
        }

        if (! empty($requestData['number'])) {
            $query->where('number', 'LIKE', $requestData['number']);
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

        $currencies = ['RUB', 'EUR'];
        foreach ($currencies as $currency) {
            $total['amount'][$currency] = 0;
            $total['amount_deposit'][$currency] = 0;
            $total['amount_commission'][$currency] = 0;
            $total['amount_acts_left_paid'][$currency] = 0;
            foreach ((clone $query)->where('currency', $currency)->get() as $guarantee) {
                $total['amount'][$currency] += $guarantee->amount;
                $total['amount_deposit'][$currency] += $guarantee->amount_deposit;
                $total['amount_commission'][$currency] += $guarantee->commission;
                $total['amount_acts_left_paid'][$currency] += $guarantee->getAvansesLeftAmount();
            }
        }

        $query->with('company', 'object', 'contract', 'contract.acts', 'contract.avansesReceived', 'organization');
        $query->orderByDesc('amount');

        return $query->paginate($perPage)->withQueryString();
    }

    public function createBankGuarantee(array $requestData): void
    {
        $guarantee = BankGuarantee::create([
            'contract_id' => $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'number' => $requestData['number'],
            'commission' => $requestData['commission'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'start_date_deposit' => $requestData['start_date_deposit'],
            'end_date_deposit' => $requestData['end_date_deposit'],
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'target' => $this->sanitizer->set($requestData['target'])->get(),
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $guarantee->addMedia($file)->toMediaCollection();
            }
        }
    }

    public function updateBankGuarantee(BankGuarantee $guarantee, array $requestData): void
    {
        $guarantee->update([
            'contract_id' => $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'number' => $requestData['number'],
            'commission' => $requestData['commission'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'start_date_deposit' => $requestData['start_date_deposit'],
            'end_date_deposit' => $requestData['end_date_deposit'],
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'target' => $this->sanitizer->set($requestData['target'])->get(),
            'status_id' => $requestData['status_id']
        ]);
    }

    public function destroyBankGuarantee(BankGuarantee $guarantee): void
    {
        $guarantee->delete();
    }
}
