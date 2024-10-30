<?php

namespace App\Services\Contract;

use App\Helpers\Sanitizer;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Currency;

class ActService
{
    private Sanitizer $sanitizer;
    private CurrencyExchangeRateService $currencyService;

    public function __construct(Sanitizer $sanitizer, CurrencyExchangeRateService $currencyService)
    {
        $this->sanitizer = $sanitizer;
        $this->currencyService = $currencyService;
    }

    public function getPivot(array | null $objectIds = null): array
    {
        $pivot = [
            'total' => [
                'acts' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'avanses' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'avanses_fix' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'avanses_float' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'gu' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
            ],
            'entries' => [],
        ];

        $objectsQuery = BObject::query();

        if ($objectIds) {
            $objectsQuery->whereIn('id', $objectIds);
        }

        $objects = $objectsQuery->active()
            ->orderByDesc('code')
            ->with('guaranteePayments')
            ->get();

        $contractService = new ContractService($this->sanitizer, $this->currencyService);

        foreach ($objects as $object) {
            $totalInfo = [];
            $contractService->filterContracts(['object_id' => [$object->id]], $totalInfo);

            $actsAmount['RUB'] = $totalInfo['avanses_acts_left_paid_amount']['RUB'] < 0 ? 0 : $totalInfo['avanses_acts_left_paid_amount']['RUB'];
            $actsAmount['EUR'] = $totalInfo['avanses_acts_left_paid_amount']['EUR'] < 0 ? 0 : $totalInfo['avanses_acts_left_paid_amount']['EUR'];

            $avansesAmount['RUB'] = $totalInfo['avanses_left_amount']['RUB'] < 0 ? 0 : $totalInfo['avanses_left_amount']['RUB'];
            $avansesAmount['EUR'] = $totalInfo['avanses_left_amount']['EUR'] < 0 ? 0 : $totalInfo['avanses_left_amount']['EUR'];

            $avansesAmountFix['RUB'] = $totalInfo['avanses_left_amount_fix']['RUB'] < 0 ? 0 : $totalInfo['avanses_left_amount_fix']['RUB'];
            $avansesAmountFix['EUR'] = $totalInfo['avanses_left_amount_fix']['EUR'] < 0 ? 0 : $totalInfo['avanses_left_amount_fix']['EUR'];

            $avansesAmountFloat['RUB'] = $totalInfo['avanses_left_amount_float']['RUB'] < 0 ? 0 : $totalInfo['avanses_left_amount_float']['RUB'];
            $avansesAmountFloat['EUR'] = $totalInfo['avanses_left_amount_float']['EUR'] < 0 ? 0 : $totalInfo['avanses_left_amount_float']['EUR'];

            $guAmount['RUB'] = $totalInfo['avanses_acts_deposites_amount']['RUB'] < 0 ? 0 : $totalInfo['avanses_acts_deposites_amount']['RUB'];
            $guAmount['EUR'] = $totalInfo['avanses_acts_deposites_amount']['EUR'] < 0 ? 0 : $totalInfo['avanses_acts_deposites_amount']['EUR'];

            if ($actsAmount['RUB'] == 0 && $actsAmount['EUR'] == 0 && $avansesAmount['RUB'] == 0 && $avansesAmount['EUR'] == 0 && $guAmount['RUB'] == 0 && $guAmount['EUR'] == 0) {
                continue;
            }

            $guAmount['RUB'] -= $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
            $guAmount['EUR'] -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount');

            $pivot['entries'][] = [
                'object' => [
                    'id' => $object->id,
                    'name' => $object->getName()
                ],
                'acts' => [
                    'RUB' => $actsAmount['RUB'],
                    'EUR' => $actsAmount['EUR'],
                ],
                'avanses' => [
                    'RUB' => $avansesAmount['RUB'],
                    'EUR' => $avansesAmount['EUR'],
                ],
                'avanses_fix' => [
                    'RUB' => $avansesAmountFix['RUB'],
                    'EUR' => $avansesAmountFix['EUR'],
                ],
                'avanses_float' => [
                    'RUB' => $avansesAmountFloat['RUB'],
                    'EUR' => $avansesAmountFloat['EUR'],
                ],
                'gu' => [
                    'RUB' => $guAmount['RUB'],
                    'EUR' => $guAmount['EUR'],
                ]
            ];

            $pivot['total']['acts']['RUB'] += $actsAmount['RUB'];
            $pivot['total']['acts']['EUR'] += $actsAmount['EUR'];
            $pivot['total']['avanses']['RUB'] += $avansesAmount['RUB'];
            $pivot['total']['avanses']['EUR'] += $avansesAmount['EUR'];
            $pivot['total']['avanses_fix']['RUB'] += $avansesAmountFix['RUB'];
            $pivot['total']['avanses_fix']['EUR'] += $avansesAmountFix['EUR'];
            $pivot['total']['avanses_float']['RUB'] += $avansesAmountFloat['RUB'];
            $pivot['total']['avanses_float']['EUR'] += $avansesAmountFloat['EUR'];
            $pivot['total']['gu']['RUB'] += $guAmount['RUB'];
            $pivot['total']['gu']['EUR'] += $guAmount['EUR'];
        }

        return $pivot;
    }

    public function filterActs(array $requestData, array &$total, bool $needPaginate = true): Collection|LengthAwarePaginator
    {
        $actQuery = Act::query();

        if (! empty($requestData['contract_id'])) {
            $actQuery->whereIn('contract_id', $requestData['contract_id']);
        }

        if (! empty($requestData['object_id'])) {
            $actQuery->whereIn('object_id', $requestData['object_id']);
        }

        if (isset($requestData['currency'])) {
            $actQuery->where('currency', $requestData['currency']);
        }

        if (! empty($requestData['sort_by'])) {
            if ($requestData['sort_by'] == 'contract_id') {
                $actQuery->orderBy(Contract::select('name')->whereColumn('contracts.id', 'acts.contract_id'), $requestData['sort_direction'] ?? 'asc');
            } elseif ($requestData['sort_by'] == 'object_id') {
                $actQuery->orderBy(BObject::select('code')->whereColumn('objects.id', 'acts.object_id'), $requestData['sort_direction'] ?? 'asc');
            } else {
                $actQuery->orderBy($requestData['sort_by'], $requestData['sort_direction'] ?? 'asc');
            }
        } else {
            $actQuery->orderByDesc('date')
                ->orderByDesc('id');
        }

        $actQuery->with('object', 'contract', 'payments');

        $perPage = 30;

        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $currencies = Currency::getCurrencies();
        foreach ($currencies as $currency) {
            $total['amount'][$currency] = 0;
            $total['avanses_amount'][$currency] = 0;
            $total['deposites_amount'][$currency] = 0;
            $total['need_paid_amount'][$currency] = 0;
            $total['paid_amount'][$currency] = 0;
            $total['left_paid_amount'][$currency] = 0;

            $total['amount_fix'][$currency] = 0;
            $total['avanses_amount_fix'][$currency] = 0;
            $total['deposites_amount_fix'][$currency] = 0;
            $total['need_paid_amount_fix'][$currency] = 0;
            $total['paid_amount_fix'][$currency] = 0;
            $total['left_paid_amount_fix'][$currency] = 0;

            $total['amount_float'][$currency] = 0;
            $total['avanses_amount_float'][$currency] = 0;
            $total['deposites_amount_float'][$currency] = 0;
            $total['need_paid_amount_float'][$currency] = 0;
            $total['paid_amount_float'][$currency] = 0;
            $total['left_paid_amount_float'][$currency] = 0;

            foreach ((clone $actQuery)->where('currency', $currency)->get() as $act) {
                $total['amount'][$currency] += $act->getAmount();
                $total['avanses_amount'][$currency] += $act->getAvansAmount();
                $total['deposites_amount'][$currency] += $act->getDepositAmount();
                $total['need_paid_amount'][$currency] += $act->getNeedPaidAmount();
                $total['paid_amount'][$currency] += $act->getPaidAmount();
                $total['left_paid_amount'][$currency] += $act->getLeftPaidAmount();

                if ($act->contract->isFloat()) {
                    $total['amount_float'][$currency] += $act->getAmount();
                    $total['avanses_amount_float'][$currency] += $act->getAvansAmount();
                    $total['deposites_amount_float'][$currency] += $act->getDepositAmount();
                    $total['need_paid_amount_float'][$currency] += $act->getNeedPaidAmount();
                    $total['paid_amount_float'][$currency] += $act->getPaidAmount();
                    $total['left_paid_amount_float'][$currency] += $act->getLeftPaidAmount();
                } else {
                    $total['amount_fix'][$currency] += $act->getAmount();
                    $total['avanses_amount_fix'][$currency] += $act->getAvansAmount();
                    $total['deposites_amount_fix'][$currency] += $act->getDepositAmount();
                    $total['need_paid_amount_fix'][$currency] += $act->getNeedPaidAmount();
                    $total['paid_amount_fix'][$currency] += $act->getPaidAmount();
                    $total['left_paid_amount_fix'][$currency] += $act->getLeftPaidAmount();
                }
            }
        }

        $total['ids'] = (clone $actQuery)->pluck('id')->toArray();

        if ($needPaginate) {
            return $actQuery->paginate($perPage)->withQueryString();
        }

        return $actQuery->get();
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
            'planned_payment_date' => $requestData['planned_payment_date'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'rad_amount' => $this->sanitizer->set($requestData['rad_amount'])->toAmount()->get(),
            'opste_amount' => $this->sanitizer->set($requestData['opste_amount'])->toAmount()->get(),
            'amount_avans' => $this->sanitizer->set($requestData['amount_avans'])->toAmount()->get(),
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => Status::STATUS_ACTIVE,
            'currency' => $contract->currency,
            'currency_rate' => $contract->currency_rate,
            'manual_left_paid_amount' => ! empty($requestData['manual_left_paid_amount']) ? $this->sanitizer->set($requestData['manual_left_paid_amount'])->toAmount()->get() : null,
        ]);

        $act->update([
            'amount_need_paid' => $act->getAmount() - $act->amount_avans - $act->amount_deposit
        ]);

        if (! empty($requestData['payments_date'])) {
            foreach ($requestData['payments_date'] as $index => $paymentDate) {
                $paymentAmount = $this->sanitizer->set($requestData['payments_amount'][$index])->toAmount()->get();
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
                            ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency) ?? 1
                            : $act->currency_rate,
                    ]);
                }
            }
        }

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $act->addMedia($file)->toMediaCollection();
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
            'planned_payment_date' => $requestData['planned_payment_date'],
            'number' => $requestData['number'],
            'amount' => $this->sanitizer->set($requestData['amount'])->toAmount()->get(),
            'rad_amount' => $this->sanitizer->set($requestData['rad_amount'])->toAmount()->get(),
            'opste_amount' => $this->sanitizer->set($requestData['opste_amount'])->toAmount()->get(),
            'amount_avans' => $this->sanitizer->set($requestData['amount_avans'])->toAmount()->get(),
            'amount_deposit' => $this->sanitizer->set($requestData['amount_deposit'])->toAmount()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->get(),
            'status_id' => $requestData['status_id'],
            'currency' => $contract->currency,
            'currency_rate' => $contract->currency_rate,
            'manual_left_paid_amount' => ! empty($requestData['manual_left_paid_amount']) ? $this->sanitizer->set($requestData['manual_left_paid_amount'])->toAmount()->get() : null,
        ]);

        $act->update([
            'amount_need_paid' => $act->getAmount() - $act->amount_avans - $act->amount_deposit
        ]);

        $currentPaymentsIds = $act->payments()->pluck('id', 'id')->toArray();

        if (! empty($requestData['isset_payments_date'])) {
            foreach ($requestData['isset_payments_date'] as $paymentId => $paymentDate) {
                $payment = ActPayment::find($paymentId);
                $paymentAmount = $this->sanitizer->set($requestData['isset_payments_amount'][$paymentId])->toAmount()->get();
                $description = $requestData['isset_payments_description'][$paymentId];

                $payment->update([
                    'date' => $paymentDate,
                    'amount' => $paymentAmount,
                    'currency' => $contract->currency,
                    'description' => $this->sanitizer->set($description)->get(),
                    'currency_rate' => $act->currency !== 'RUB'
                        ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency) ?? 1
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
                $paymentAmount = $this->sanitizer->set($requestData['payments_amount'][$index])->toAmount()->get();
                $description = $requestData['payments_description'][$index];

                if ($paymentAmount > 0) {
                    ActPayment::create([
                        'contract_id' => $contract->id,
                        'act_id' => $act->id,
                        'company_id' => $act->company_id,
                        'object_id' => $act->object_id,
                        'date' => $paymentDate,
                        'description' => $this->sanitizer->set($description)->get(),
                        'amount' => $paymentAmount,
                        'status_id' => Status::STATUS_ACTIVE,
                        'currency' => $contract->currency,
                        'currency_rate' => $act->currency !== 'RUB'
                            ? $this->currencyService->parseRateFromCBR($paymentDate, $act->currency) ?? 1
                            : $act->currency_rate,
                    ]);
                }
            }
        }

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $act->addMedia($file)->toMediaCollection();
            }
        }
    }

    public function destroyAct(Act $act): void
    {
        $act->payments()->delete();
        $act->delete();
    }

    public function prepareInfoForActsPaymentsLineChart(array $actIds): array
    {
        $info = [];

        $actsMonths = [];
        foreach (Act::whereIn('id', $actIds)->orderBy('date')->get() as $act) {
            $month = Carbon::parse($act->date)->format('F Y');

            if (! isset($actsMonths[$month][$act->currency])) {
                $actsMonths[$month][$act->currency] = 0;
            }

            $actsMonths[$month][$act->currency] += $act->getAmount();
        }

        foreach ($actsMonths as $month => $currencies) {
            $amount = $currencies['RUB'] ?? 0;
            if ($amount !== 0) {
                $info['rub_months'][] = $month;
                $info['rub_amounts'][] = $amount;
            }

            $amount = $currencies['EUR'] ?? 0;
            if ($amount !== 0) {
                $info['eur_months'][] = $month;
                $info['eur_amounts'][] = $amount;
            }
        }

        return $info;
    }
}
