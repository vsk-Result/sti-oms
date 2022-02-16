<?php

namespace App\Services\Contract;

use App\Helpers\Sanitizer;
use App\Models\Contract\Contract;
use App\Models\Contract\ContractAvans;
use App\Models\Contract\ContractReceivedAvans;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;

class ContractService
{
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $currencyService;

    public function __construct(Sanitizer $sanitizer, CurrencyExchangeRateService $currencyService)
    {
        $this->sanitizer = $sanitizer;
        $this->currencyService = $currencyService;
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
                if ($avansAmount > 0) {
                    ContractReceivedAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $requestData['company_id'],
                        'object_id' => $requestData['object_id'],
                        'date' => $avansDate,
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

        if (! empty($requestData['isset_avanses'])) {
            foreach ($requestData['isset_avanses'] as $avansId => $avansAmount) {
                $avans = ContractAvans::find($avansId);
                if ((float) $avansAmount > 0) {
                    $avans->update([
                        'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                        'currency' => $contract->currency,
                    ]);
                } else {
                    $avans->delete();
                }
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

        if (! empty($requestData['isset_received_avanses_date'])) {
            foreach ($requestData['isset_received_avanses_date'] as $avansId => $avansDate) {
                $avans = ContractReceivedAvans::find($avansId);
                $avansAmount = (float) $requestData['isset_received_avanses_amount'][$avansId];

                if ($avansAmount > 0) {
                    $avans->update([
                        'date' => $avansDate,
                        'amount' => $this->sanitizer->set($avansAmount)->toAmount()->get(),
                        'currency' => $contract->currency,
                        'currency_rate' => $contract->currency !== 'RUB'
                            ? $this->currencyService->parseRateFromCBR($avansDate, $contract->currency)
                            : $contract->currency_rate,
                    ]);
                } else {
                    $avans->delete();
                }
            }
        }

        if (! empty($requestData['received_avanses_date'])) {
            foreach ($requestData['received_avanses_date'] as $index => $avansDate) {
                $avansAmount = (float) $requestData['received_avanses_amount'][$index];
                if ($avansAmount > 0) {
                    ContractReceivedAvans::create([
                        'contract_id' => $contract->id,
                        'company_id' => $requestData['company_id'],
                        'object_id' => $requestData['object_id'],
                        'date' => $avansDate,
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
