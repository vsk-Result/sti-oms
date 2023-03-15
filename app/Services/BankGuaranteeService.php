<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\BankGuarantee;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Currency;

class BankGuaranteeService
{
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $rateService;

    public function __construct(Sanitizer $sanitizer, CurrencyExchangeRateService $rateService)
    {
        $this->sanitizer = $sanitizer;
        $this->rateService = $rateService;
    }

    public function filterBankGuarantee(array $requestData, array &$total = [], bool $needPaginate = true): LengthAwarePaginator|Collection
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

        if (! empty($requestData['currency'])) {
            $query->whereIn('currency', $requestData['currency']);
        }

        if (! empty($requestData['contract_id'])) {
            $query->whereIn('contract_id', $requestData['contract_id']);
        }

        if (! empty($requestData['status_id'])) {
            $query->whereIn('status_id', $requestData['status_id']);
        }

        if (! empty($requestData['object_id'])) {
            $query->whereIn('object_id', $requestData['object_id']);
        } else {
            if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
                $query->whereIn('object_id', auth()->user()->objects->pluck('id'));
            }
        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $currencies = Currency::getCurrencies();
        foreach ($currencies as $currency) {
            $bgQuery = (clone $query)->where('currency', $currency);

            $total['amount']['active'][$currency] = (clone $bgQuery)->where('end_date', '>=', Carbon::now())->sum('amount');
            $total['amount']['expired'][$currency] = (clone $bgQuery)->where('end_date', '<', Carbon::now())->sum('amount');

            $total['amount_deposit']['active'][$currency] = (clone $bgQuery)->where('end_date_deposit', '>=', Carbon::now())->sum('amount_deposit');
            $total['amount_deposit']['expired'][$currency] = (clone $bgQuery)->where('end_date_deposit', '<', Carbon::now())->sum('amount_deposit');
        }

        $query->with('company', 'object', 'contract', 'contract.acts', 'contract.avansesReceived', 'organization');
        $query->orderByDesc('object_id');

        return $needPaginate ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function createBankGuarantee(array $requestData): void
    {
        $currency = $requestData['currency'] ?? 'RUB';
        $currencyRate = $currency !== 'RUB' && ! empty($requestData['start_date'])
            ? $this->rateService->getExchangeRate($requestData['start_date'], $currency)->rate ?? 0
            : 1;

        $guarantee = BankGuarantee::create([
            'contract_id' => $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'number' => $requestData['number'],
            'commission' => $this->sanitizer->set($requestData['commission'])->toAmount()->get(),
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'start_date_deposit' => $requestData['start_date_deposit'],
            'end_date_deposit' => $requestData['end_date_deposit'],
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'target' => $this->sanitizer->set($requestData['target'])->get(),
            'status_id' => Status::STATUS_ACTIVE,
            'currency' => $currency,
            'currency_rate' => $currencyRate,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $guarantee->addMedia($file)->toMediaCollection();
            }
        }
    }

    public function updateBankGuarantee(BankGuarantee $guarantee, array $requestData): void
    {
        $currency = $requestData['currency'] ?? 'RUB';
        $currencyRate = $currency !== 'RUB' && ! empty($requestData['start_date'])
            ? $this->rateService->getExchangeRate($requestData['start_date'], $currency)->rate ?? 0
            : 1;

        $guarantee->update([
            'contract_id' => $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'number' => $requestData['number'],
            'commission' => $this->sanitizer->set($requestData['commission'])->toAmount()->get(),
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'start_date_deposit' => $requestData['start_date_deposit'],
            'end_date_deposit' => $requestData['end_date_deposit'],
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'target' => $this->sanitizer->set($requestData['target'])->get(),
            'status_id' => $requestData['status_id'],
            'currency' => $currency,
            'currency_rate' => $currencyRate,
        ]);
    }

    public function destroyBankGuarantee(BankGuarantee $guarantee): void
    {
        $guarantee->delete();
    }

    public function checkExpired(): array
    {
        $bgInfo = [];
        $depositInfo = [];

        $expiredBankGuarantees = BankGuarantee::where('status_id', Status::STATUS_ACTIVE)
            ->where('end_date', '<', Carbon::now())
            ->get();
        foreach ($expiredBankGuarantees as $guarantee) {
            $info = [
                'id' => $guarantee->id,
                'number' => $guarantee->number,
                'date' => $guarantee->end_date,
                'object_id' => $guarantee->object_id,
            ];
            $guarantee->update([
                'status_id' => Status::STATUS_BLOCKED
            ]);
            $bgInfo[] = $info;
            break;
        }

        $expiredDeposites = BankGuarantee::where('status_id', Status::STATUS_ACTIVE)
            ->where('end_date_deposit', '<', Carbon::now())
            ->get();
        foreach ($expiredDeposites as $guarantee) {
            $info = [
                'id' => $guarantee->id,
                'number' => $guarantee->number,
                'date' => $guarantee->end_date_deposit,
                'object_id' => $guarantee->object_id,
            ];
            $guarantee->update([
                'status_id' => Status::STATUS_BLOCKED
            ]);
            $depositInfo[] = $info;
            break;
        }

        return [
            'bg' => $bgInfo,
            'deposit' => $depositInfo,
        ];
    }
}
