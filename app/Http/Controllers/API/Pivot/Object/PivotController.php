<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ContractService;
use App\Services\CurrencyExchangeRateService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PivotController extends Controller
{
    private ContractService $contractService;
    private CurrencyExchangeRateService $rateService;

    public function __construct(ContractService $contractService, CurrencyExchangeRateService $rateService)
    {
        $this->contractService = $contractService;
        $this->rateService = $rateService;
    }

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if (empty($request->object_id)) {
            abort(404);
            return response()->json([], 404);
        }

        $object = BObject::find($request->object_id);

        if (! $object) {
            abort(404);
            return response()->json([], 404);
        }

        $contractsTotal = [];
        $this->contractService->filterContracts(['object_id' => [$object->id]], $contractsTotal);
        $currentRate = $this->rateService->getExchangeRate(Carbon::now()->format('Y-m-d'), 'EUR');

        $contractsTotalRUB = $contractsTotal['amount']['RUB'];
        $contractsTotalEUR = $contractsTotal['amount']['EUR'];

        if ($currentRate) {
            $contractsTotalRUB = $contractsTotalRUB + $contractsTotalEUR * $currentRate->rate;
            $contractsTotalEUR = 0;
        }

        $paymentQuery = Payment::select('object_id', 'amount');
        $pay = (clone $paymentQuery)->where('object_id', $object->id)->where('amount', '<', 0)->sum('amount');
        $receive = (clone $paymentQuery)->where('object_id', $object->id)->sum('amount') - $pay;

        $avansesNotworkLeftAmountRUB = $contractsTotal['avanses_notwork_left_amount']['RUB'];
        $avansesNotworkLeftAmountEUR = $contractsTotal['avanses_notwork_left_amount']['EUR'];

        if ($currentRate) {
            $avansesNotworkLeftAmountRUB = $avansesNotworkLeftAmountRUB + $avansesNotworkLeftAmountEUR * $currentRate->rate;
            $avansesNotworkLeftAmountEUR = 0;
        }

        $balance = $pay + $receive;
        $generalCosts = $object->generalCosts()->sum('amount');

        $customerDebtRUB = $contractsTotal['avanses_acts_left_paid_amount']['RUB'] + $contractsTotal['avanses_left_amount']['RUB'] + $contractsTotal['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
        $customerDebtEUR = $contractsTotal['avanses_acts_left_paid_amount']['EUR'] + $contractsTotal['avanses_left_amount']['EUR'] + $contractsTotal['avanses_acts_deposites_amount']['EUR'] - $object->guaranteePayments->where('currency', 'EUR')->sum('amount');

        if ($currentRate) {
            $customerDebtRUB = $customerDebtRUB + $customerDebtEUR * $currentRate->rate;
            $customerDebtEUR = 0;
        }

        $contractorDebtsAmount = $object->getContractorDebtsAmount(true);
        $providerDebtsAmount = $object->getProviderDebtsAmount();

        $workSalaryDebt = $object->getWorkSalaryDebt();
        $ITRSalaryDebt = $object->getITRSalaryDebt();

        $objectBalanceRUB = $generalCosts + $balance +
            $contractsTotal['avanses_left_amount']['RUB'] +
            $contractsTotal['avanses_acts_left_paid_amount']['RUB'] +
            $contractsTotal['avanses_acts_deposites_amount']['RUB'] +
            $contractorDebtsAmount +
            $providerDebtsAmount +
            $ITRSalaryDebt +
            $workSalaryDebt;

        $objectBalanceEUR = $contractsTotal['avanses_left_amount']['EUR'] + $contractsTotal['avanses_acts_left_paid_amount']['EUR'] + $contractsTotal['avanses_acts_deposites_amount']['EUR'];

        if ($currentRate) {
            $objectBalanceRUB = $objectBalanceRUB + $objectBalanceEUR * $currentRate->rate;
            $objectBalanceEUR = 0;
        }

        $promBalanceRUB = $balance + $generalCosts +
            $contractsTotal['avanses_left_amount']['RUB'] +
            $contractsTotal['avanses_acts_left_paid_amount']['RUB'] +
            $contractsTotal['avanses_acts_deposites_amount']['RUB'] +
            $contractorDebtsAmount +
            $providerDebtsAmount +
            $ITRSalaryDebt +
            $workSalaryDebt +
            $contractsTotal['avanses_non_closes_amount']['RUB'] -
            $contractsTotal['avanses_left_amount']['RUB'];

        $promBalanceEUR = $contractsTotal['avanses_left_amount']['EUR'] +
            $contractsTotal['avanses_acts_left_paid_amount']['EUR'] +
            $contractsTotal['avanses_acts_deposites_amount']['EUR'] +
            $contractsTotal['avanses_non_closes_amount']['EUR'] -
            $contractsTotal['avanses_left_amount']['EUR'];

        if ($currentRate) {
            $promBalanceRUB = $promBalanceRUB + $promBalanceEUR * $currentRate->rate;
            $promBalanceEUR = 0;
        }

        $guRUB = $contractsTotal['avanses_non_closes_amount']['RUB'];
        $guEUR = $contractsTotal['avanses_non_closes_amount']['EUR'];

        if ($currentRate) {
            $guRUB = $guRUB + $guEUR * $currentRate->rate;
            $guEUR = 0;
        }

        $info = [
            'name' => $object->getName(),
            'contracts_amount_rub' => CurrencyExchangeRate::format($contractsTotalRUB, 'RUB'),
            'contracts_amount_eur' => CurrencyExchangeRate::format($contractsTotalEUR, 'EUR'),
            'pay' => CurrencyExchangeRate::format($pay, 'RUB'),
            'receive' => CurrencyExchangeRate::format($receive, 'RUB'),
            'avanses_notwork_left_amount_rub' => CurrencyExchangeRate::format($avansesNotworkLeftAmountRUB, 'RUB'),
            'avanses_notwork_left_amount_eur' => CurrencyExchangeRate::format($avansesNotworkLeftAmountEUR, 'EUR'),
            'balance' => CurrencyExchangeRate::format($balance, 'RUB'),
            'general_costs' => CurrencyExchangeRate::format($generalCosts, 'RUB'),
            'general_costs_with_balance' => CurrencyExchangeRate::format($generalCosts + $balance, 'RUB'),
            'contractors_debts' => CurrencyExchangeRate::format($contractorDebtsAmount, 'RUB'),
            'providers_debts' => CurrencyExchangeRate::format($providerDebtsAmount, 'RUB'),
            'workers_debts' => CurrencyExchangeRate::format($workSalaryDebt, 'RUB'),
            'customer_debts_rub' => CurrencyExchangeRate::format($customerDebtRUB, 'RUB'),
            'customer_debts_eur' => CurrencyExchangeRate::format($customerDebtEUR, 'EUR'),
            'object_balance_rub' => CurrencyExchangeRate::format($objectBalanceRUB, 'RUB'),
            'object_balance_eur' => CurrencyExchangeRate::format($objectBalanceEUR, 'EUR'),
            'prom_balance_rub' => CurrencyExchangeRate::format($promBalanceRUB, 'RUB'),
            'prom_balance_eur' => CurrencyExchangeRate::format($promBalanceEUR, 'EUR'),
            'gu_rub' => CurrencyExchangeRate::format($guRUB, 'RUB'),
            'gu_eur' => CurrencyExchangeRate::format($guEUR, 'EUR'),
            'get_eur_rate' => (bool)$currentRate
        ];

        return response()->json(compact('info'));
    }
}