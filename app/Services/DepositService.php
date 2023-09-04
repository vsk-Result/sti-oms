<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Deposit;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Currency;

class DepositService
{
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $rateService;

    public function __construct(Sanitizer $sanitizer, CurrencyExchangeRateService $rateService)
    {
        $this->sanitizer = $sanitizer;
        $this->rateService = $rateService;
    }

    public function filterDeposit(array $requestData, array &$total = [], bool $needPaginate = true): LengthAwarePaginator|Collection
    {
        $query = Deposit::query();

        if (! empty($requestData['period'])) {
            $period = explode(' - ', $requestData['period']);
            $query->whereBetween('start_date', [Carbon::parse($period[0]), Carbon::parse($period[1])]);
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
            if (auth()->user() && auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
                $query->whereIn('object_id', auth()->user()->objects->pluck('id'));
            }
        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $currencies = Currency::getCurrencies();
        foreach ($currencies as $currency) {
            $dQuery = (clone $query)->where('currency', $currency);

            $total['amount']['active'][$currency] = (clone $dQuery)->where('end_date', '>=', Carbon::now())->sum('amount');
            $total['amount']['expired'][$currency] = (clone $dQuery)->where('end_date', '<', Carbon::now())->sum('amount');
        }

        $query->with('company', 'object', 'contract', 'contract.acts', 'contract.avansesReceived', 'organization');
        $query->orderByDesc('object_id');

        return $needPaginate ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function createDeposit(array $requestData): void
    {
        $currency = $requestData['currency'] ?? 'RUB';
        $currencyRate = $currency !== 'RUB' && ! empty($requestData['start_date'])
            ? $this->rateService->getExchangeRate($requestData['start_date'], $currency)->rate ?? 0
            : 1;

        $deposit = Deposit::create([
            'contract_id' => $requestData['contract_id'] === 'null' ? null : $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'] === 'null' ? null : $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'status_id' => Status::STATUS_ACTIVE,
            'currency' => $currency,
            'currency_rate' => $currencyRate,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $deposit->addMedia($file)->toMediaCollection();
            }
        }
    }

    public function updateDeposit(Deposit $deposit, array $requestData): void
    {
        $currency = $requestData['currency'] ?? 'RUB';
        $currencyRate = $currency !== 'RUB' && ! empty($requestData['start_date'])
            ? $this->rateService->getExchangeRate($requestData['start_date'], $currency)->rate ?? 0
            : 1;

        $deposit->update([
            'contract_id' => $requestData['contract_id'] === 'null' ? null : $requestData['contract_id'],
            'organization_id' => $requestData['organization_id'] === 'null' ? null : $requestData['organization_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'status_id' => $requestData['status_id'],
            'currency' => $currency,
            'currency_rate' => $currencyRate,
        ]);
    }

    public function destroyDeposit(Deposit $deposit): void
    {
        $deposit->delete();
    }

    public function checkExpired(): array
    {
        $depositInfo = [];

        $expiredDeposits = Deposit::where('status_id', Status::STATUS_ACTIVE)
            ->where('end_date', '<', Carbon::now())
            ->get();
        foreach ($expiredDeposits as $deposit) {
            $info = [
                'id' => $deposit->id,
                'date' => $deposit->end_date,
                'object_id' => $deposit->object_id,
            ];
            $deposit->update([
                'status_id' => Status::STATUS_BLOCKED
            ]);
            $depositInfo[] = $info;
            break;
        }

        return [
            'deposit' => $depositInfo,
        ];
    }
}
