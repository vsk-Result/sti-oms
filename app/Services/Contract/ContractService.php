<?php

namespace App\Services\Contract;

use App\Helpers\Sanitizer;
use App\Models\Contract\Contract;
use App\Models\Contract\ContractAvans;
use App\Models\Contract\ContractReceivedAvans;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Currency;

class ContractService
{
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $currencyService;

    public function __construct(Sanitizer $sanitizer, CurrencyExchangeRateService $currencyService)
    {
        $this->sanitizer = $sanitizer;
        $this->currencyService = $currencyService;
    }

    public function filterContracts(array $requestData, array &$total): LengthAwarePaginator
    {
        $contractQuery = Contract::query();

        $contractQuery->where('type_id', Contract::TYPE_MAIN);

        if (! empty($requestData['name'])) {
            $contractQuery->where('name', 'LIKE', '%' . $requestData['name'] . '%');
        }

        if (! empty($requestData['object_id'])) {
            $contractQuery->whereIn('object_id', $requestData['object_id']);
        }

        $contractQuery->with('object', 'children', 'acts', 'avanses', 'avansesReceived', 'acts.payments', 'children.acts', 'children.avanses', 'children.avansesReceived', 'children.acts.payments');

        $perPage = 30;

        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $currencies = Currency::getCurrencies();
        foreach ($currencies as $currency) {
            $total['amount'][$currency] = 0;
            $total['avanses_amount'][$currency] = 0;
            $total['avanses_received_amount'][$currency] = 0;
            $total['avanses_left_amount'][$currency] = 0;
            $total['acts_amount'][$currency] = 0;
            $total['avanses_acts_paid_amount'][$currency] = 0;
            $total['avanses_acts_left_paid_amount'][$currency] = 0;
            $total['avanses_acts_deposites_amount'][$currency] = 0;
            $total['avanses_acts_avanses_amount'][$currency] = 0;

            foreach ((clone $contractQuery)->where('object_id', '!=', 16)->where('currency', $currency)->get() as $contract) {
                $total['amount'][$currency] += $contract->getAmount($currency);
                $total['avanses_amount'][$currency] += $contract->getAvansesAmount($currency);
                $total['avanses_received_amount'][$currency] += $contract->getAvansesReceivedAmount($currency);
                $total['avanses_left_amount'][$currency] += $contract->getAvansesLeftAmount($currency);
                $total['acts_amount'][$currency] += $contract->getActsAmount($currency);
                $total['avanses_acts_paid_amount'][$currency] += $contract->getActsPaidAmount($currency);
                $total['avanses_acts_left_paid_amount'][$currency] += $contract->getActsLeftPaidAmount($currency);
                $total['avanses_acts_deposites_amount'][$currency] += $contract->getActsDepositesAmount($currency);
                $total['avanses_acts_avanses_amount'][$currency] += $contract->getActsAvasesAmount($currency);
            }

            foreach ((clone $contractQuery)->where('object_id', 16)->get() as $contract) {
                $total['amount'][$currency] += $contract->getAmount($currency);
                $total['avanses_amount'][$currency] += $contract->getAvansesAmount($currency);
                $total['avanses_received_amount'][$currency] += $contract->getAvansesReceivedAmount($currency);
                $total['avanses_left_amount'][$currency] += $contract->getAvansesLeftAmount($currency);
                $total['acts_amount'][$currency] += $contract->getActsAmount($currency);
                $total['avanses_acts_paid_amount'][$currency] += $contract->getActsPaidAmount($currency);
                $total['avanses_acts_left_paid_amount'][$currency] += $contract->getActsLeftPaidAmount($currency);
                $total['avanses_acts_deposites_amount'][$currency] += $contract->getActsDepositesAmount($currency);
                $total['avanses_acts_avanses_amount'][$currency] += $contract->getActsAvasesAmount($currency);
            }

            $total['avanses_notwork_left_amount'][$currency] = $total['avanses_received_amount'][$currency] - $total['avanses_acts_avanses_amount'][$currency];
            $total['avanses_non_closes_amount'][$currency] = $total['amount'][$currency] - $total['avanses_received_amount'][$currency] - $total['avanses_acts_paid_amount'][$currency] - $total['avanses_acts_left_paid_amount'][$currency];
            $total['avanses_non_closes_amount'][$currency] = abs($total['avanses_non_closes_amount'][$currency]);
        }

        $total['act_ids'] = [];
        foreach ((clone $contractQuery)->with('children')->get() as $contract) {
            $total['act_ids'] = array_merge($total['act_ids'], $contract->acts->pluck('id')->toArray());
            foreach ($contract->children as $chContract) {
                $total['act_ids'] = array_merge($total['act_ids'], $chContract->acts->pluck('id')->toArray());
            }
        }

        return $contractQuery->paginate($perPage)->withQueryString();
    }

    public function createContract(array $requestData): void
    {
        $contract = Contract::create([
            'parent_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? $requestData['parent_id'] : null,
            'type_id' => $requestData['type_id'],
            'amount_type_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? $requestData['amount_type_id'] : null,
            'company_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? Contract::find($requestData['parent_id'])->company_id : $requestData['company_id'],
            'object_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? Contract::find($requestData['parent_id'])->object_id : $requestData['object_id'],
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'params' => $requestData['params'] ?? null,
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'stage_id' => 0,
            'status_id' => Status::STATUS_ACTIVE,
            'currency' => $requestData['currency'],
            'currency_rate' => 1,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $contract->addMedia($file)->toMediaCollection();
            }
        }

        if (! empty($requestData['avanses'])) {
            foreach ($requestData['avanses'] as $avansAmount) {
                if ((float) $avansAmount > 0) {
                    ContractAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $requestData['company_id'],
                        'object_id' => $requestData['object_id'],
                        'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency_rate,
                    ]);
                }
            }
        }

        if (! empty($requestData['received_avanses_date'])) {
            foreach ($requestData['received_avanses_date'] as $index => $avansDate) {
                $avansAmount = (float) $requestData['received_avanses_amount'][$index];
                $description = $requestData['received_avanses_description'][$index];
                if ($avansAmount > 0) {
                    ContractReceivedAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $requestData['company_id'],
                        'object_id' => $requestData['object_id'],
                        'date' => $avansDate,
                        'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                        'description' => $this->sanitizer->set($description)->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency !== 'RUB'
                            ? $this->currencyService->parseRateFromCBR($avansDate, $contract->currency)
                            : $contract->currency_rate,
                    ]);
                }
            }
        }
    }

    public function updateContract(Contract $contract, array $requestData): void
    {
        $contract->update([
            'parent_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? $requestData['parent_id'] : null,
            'type_id' => $requestData['type_id'],
            'amount_type_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? $requestData['amount_type_id'] : null,
            'company_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? Contract::find($requestData['parent_id'])->company_id : $requestData['company_id'],
            'object_id' => (int) $requestData['type_id'] !== Contract::TYPE_MAIN ? Contract::find($requestData['parent_id'])->object_id : $requestData['object_id'],
            'name' => $this->sanitizer->set($requestData['name'])->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'start_date' => $requestData['start_date'],
            'end_date' => $requestData['end_date'],
            'params' => $requestData['params'] ?? $contract->params,
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'stage_id' => 0,
            'status_id' => $requestData['status_id'],
            'currency' => $requestData['currency'],
            'currency_rate' => 1,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $contract->addMedia($file)->toMediaCollection();
            }
        }

        $currentAvansesIds = $contract->avanses()->pluck('id', 'id')->toArray();

        if (! empty($requestData['isset_avanses'])) {
            foreach ($requestData['isset_avanses'] as $avansId => $avansAmount) {
                $avans = ContractAvans::find($avansId);
                $avans->update([
                    'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                    'currency' => $contract->currency,
                ]);
                unset($currentAvansesIds[$avansId]);
            }
        }

        foreach ($currentAvansesIds as $avansId) {
            $avans = ContractAvans::find($avansId);
            $avans->delete();
        }

        if (! empty($requestData['avanses'])) {
            foreach ($requestData['avanses'] as $avansAmount) {
                if ((float) $avansAmount > 0) {
                    ContractAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $requestData['company_id'],
                        'object_id' => $requestData['object_id'],
                        'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency_rate,
                    ]);
                }
            }
        }

        $currentAvansesIds = $contract->avansesReceived()->pluck('id', 'id')->toArray();

        if (! empty($requestData['isset_received_avanses_date'])) {
            foreach ($requestData['isset_received_avanses_date'] as $avansId => $avansDate) {
                $avans = ContractReceivedAvans::find($avansId);
                $avansAmount = (float) $requestData['isset_received_avanses_amount'][$avansId];
                $description = $requestData['isset_received_avanses_description'][$avansId];

                $avans->update([
                    'date' => $avansDate,
                    'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                    'currency' => $contract->currency,
                    'description' => $this->sanitizer->set($description)->get(),
                    'currency_rate' => $contract->currency !== 'RUB'
                        ? $this->currencyService->parseRateFromCBR($avansDate, $contract->currency)
                        : $contract->currency_rate,
                ]);
                unset($currentAvansesIds[$avansId]);
            }
        }

        foreach ($currentAvansesIds as $avansId) {
            $avans = ContractReceivedAvans::find($avansId);
            $avans->delete();
        }

        if (! empty($requestData['received_avanses_date'])) {
            foreach ($requestData['received_avanses_date'] as $index => $avansDate) {
                $avansAmount = (float) $requestData['received_avanses_amount'][$index];
                $description = $requestData['received_avanses_description'][$index];
                if ($avansAmount > 0) {
                    ContractReceivedAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $requestData['company_id'],
                        'object_id' => $requestData['object_id'],
                        'date' => $avansDate,
                        'description' => $this->sanitizer->set($description)->get(),
                        'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency !== 'RUB'
                            ? $this->currencyService->parseRateFromCBR($avansDate, $contract->currency)
                            : $contract->currency_rate,
                    ]);
                }
            }
        }
    }

    public function destroyContract(Contract $contract): void
    {
        foreach ($contract->children as $child) {
            $child->avanses()->delete();
            $child->avansesReceived()->delete();
            $child->actPayments()->delete();
            $child->acts()->delete();
        }

        $contract->avanses()->delete();
        $contract->avansesReceived()->delete();
        $contract->actPayments()->delete();
        $contract->acts()->delete();

        $contract->delete();
    }
}
