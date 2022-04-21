<?php

namespace App\Services\Contract;

use App\Helpers\Sanitizer;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use Illuminate\Pagination\LengthAwarePaginator;

class ActService
{
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $currencyService;

    public function __construct(Sanitizer $sanitizer, CurrencyExchangeRateService $currencyService)
    {
        $this->sanitizer = $sanitizer;
        $this->currencyService = $currencyService;
    }

    public function filterActs(array $requestData, array &$total): LengthAwarePaginator
    {
        $actQuery = Act::query();

        if (! empty($requestData['contract_id'])) {
            $actQuery->whereIn('contract_id', $requestData['contract_id']);
        }

        if (! empty($requestData['object_id'])) {
            $actQuery->whereIn('object_id', $requestData['object_id']);
        }

        $actQuery->with('object', 'contract', 'payments');

        $perPage = 30;

        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $currencies = ['RUB', 'EUR'];
        foreach ($currencies as $currency) {
            $total['amount'][$currency] = 0;
            $total['avanses_amount'][$currency] = 0;
            $total['deposites_amount'][$currency] = 0;
            $total['need_paid_amount'][$currency] = 0;
            $total['paid_amount'][$currency] = 0;
            $total['left_paid_amount'][$currency] = 0;
            foreach ((clone $actQuery)->where('currency', $currency)->get() as $act) {
                $total['amount'][$currency] += $act->getAmount();
                $total['avanses_amount'][$currency] += $act->getAvansAmount();
                $total['deposites_amount'][$currency] += $act->getDepositAmount();
                $total['need_paid_amount'][$currency] += $act->getNeedPaidAmount();
                $total['paid_amount'][$currency] += $act->getPaidAmount();
                $total['left_paid_amount'][$currency] += $act->getLeftPaidAmount();
            }
        }

        return $actQuery->paginate($perPage)->withQueryString();
    }

    public function createAct(array $requestData): void
    {
        $contract = Contract::find($requestData['contract_id']);
        $act = Act::create([
            'contract_id' => $contract->id,
            'company_id' => $contract->company_id,
            'object_id' => $contract->object_id,
            'number' => $requestData['number'],
            'date' => $requestData['date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'amount_avans' => $this->sanitizer->set($requestData['amount_avans'])->toAmount()->get(),
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => Status::STATUS_ACTIVE,
            'currency' => $contract->currency,
            'currency_rate' => $contract->currency_rate,
        ]);

        $act->update([
            'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
        ]);

        if (! empty($requestData['payments_date'])) {
            foreach ($requestData['payments_date'] as $index => $paymentDate) {
                $paymentAmount = (float) $requestData['payments_amount'][$index];
                $description = $requestData['payments_description'][$index];
                if ($paymentAmount > 0) {
                    ActPayment::create([
                        'contract_id' => $contract->id,
                        'act_id' => $act->id,
                        'company_id' => $act->company_id,
                        'object_id' => $act->object_id,
                        'date' => $paymentDate,
                        'description' => $this->sanitizer->set($description)->get(),
                        'amount' => $this->sanitizer->set($paymentAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $act->currency,
                        'currency_rate' => $act->currency !== 'RUB'
                            ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency)
                            : $act->currency_rate,
                    ]);
                }
            }
        }
    }

    public function updateAct(Act $act, array $requestData): void
    {
        $contract = Contract::find($requestData['contract_id']);
        $act->update([
            'contract_id' => $contract->id,
            'company_id' => $contract->company_id,
            'object_id' => $contract->object_id,
            'date' => $requestData['date'],
            'number' => $requestData['number'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'amount_avans' => $this->sanitizer->set($requestData['amount_avans'])->toAmount()->get(),
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => $requestData['status_id'],
            'currency' => $contract->currency,
            'currency_rate' => $contract->currency_rate,
        ]);

        $act->update([
            'amount_need_paid' => $act->amount - $act->amount_avans - $act->amount_deposit
        ]);

        $currentPaymentsIds = $act->payments()->pluck('id', 'id')->toArray();

        if (! empty($requestData['isset_payments_date'])) {
            foreach ($requestData['isset_payments_date'] as $paymentId => $paymentDate) {
                $payment = ActPayment::find($paymentId);
                $paymentAmount = (float) $requestData['isset_payments_amount'][$paymentId];
                $description = $requestData['isset_payments_description'][$paymentId];

                $payment->update([
                    'date' => $paymentDate,
                    'amount' => $this->sanitizer->set($paymentAmount)->toAmount()->get(),
                    'currency' => $contract->currency,
                    'description' => $this->sanitizer->set($description)->get(),
                    'currency_rate' => $act->currency !== 'RUB'
                        ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency)
                        : $act->currency_rate,
                ]);

                unset($currentPaymentsIds[$paymentId]);
            }
        }

        foreach ($currentPaymentsIds as $paymentId) {
            $payment = ActPayment::find($paymentId);
            $payment->delete();
        }

        if (! empty($requestData['payments_date'])) {
            foreach ($requestData['payments_date'] as $index => $paymentDate) {
                $paymentAmount = (float) $requestData['payments_amount'][$index];
                $description = $requestData['payments_description'][$index];
                if ($paymentAmount > 0) {
                    ActPayment::create([
                        'contract_id' => $contract->id,
                        'act_id' => $act->id,
                        'company_id' => $act->company_id,
                        'object_id' => $act->object_id,
                        'date' => $paymentDate,
                        'description' => $this->sanitizer->set($description)->get(),
                        'amount' => $this->sanitizer->set($paymentAmount)->toAmount()->get(),
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $act->currency !== 'RUB'
                            ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency)
                            : $act->currency_rate,
                    ]);
                }
            }
        }
    }

    public function destroyAct(Act $act): void
    {
        $act->payments()->delete();
        $act->delete();
    }
}
