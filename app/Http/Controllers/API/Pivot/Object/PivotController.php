<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ContractService;
use App\Services\CurrencyExchangeRateService;
use App\Services\PivotObjectDebtService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PivotController extends Controller
{
    private ContractService $contractService;
    private CurrencyExchangeRateService $rateService;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(
        ContractService $contractService,
        CurrencyExchangeRateService $rateService,
        PivotObjectDebtService $pivotObjectDebtService
    ) {
        $this->contractService = $contractService;
        $this->rateService = $rateService;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
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

        $customerAvansLeftDebtRUB = $contractsTotal['avanses_left_amount']['RUB'];
        $customerAvansLeftDebtEUR = $contractsTotal['avanses_left_amount']['EUR'];

        $customerActDebtRUB = $contractsTotal['avanses_acts_left_paid_amount']['RUB'];
        $customerActDebtEUR = $contractsTotal['avanses_acts_left_paid_amount']['EUR'];

        $customerActGUDebtRUB = $contractsTotal['avanses_acts_deposites_amount']['RUB'];
        $customerActGUDebtEUR = $contractsTotal['avanses_acts_deposites_amount']['EUR'];

        $customerGUReceiveRUB = $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
        $customerGUReceiveEUR = $object->guaranteePayments->where('currency', 'EUR')->sum('amount');

        if ($currentRate) {
            $customerDebtRUB = $customerDebtRUB + $customerDebtEUR * $currentRate->rate;
            $customerDebtEUR = 0;

            $customerActDebtRUB = $customerActDebtRUB + $customerActDebtEUR * $currentRate->rate;
            $customerActDebtEUR = 0;

            $customerAvansLeftDebtRUB = $customerAvansLeftDebtRUB + $customerAvansLeftDebtEUR * $currentRate->rate;
            $customerAvansLeftDebtEUR = 0;

            $customerActGUDebtRUB = $customerActGUDebtRUB + $customerActGUDebtEUR * $currentRate->rate;
            $customerActGUDebtEUR = 0;

            $customerGUReceiveRUB = $customerGUReceiveRUB + $customerGUReceiveEUR * $currentRate->rate;
            $customerGUReceiveEUR = 0;
        }

        $debts = $this->pivotObjectDebtService->getPivotDebtForObject($object->id);

        $contractorDebtsAmount = $debts['contractor']->total_amount;

        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

        if ($objectExistInObjectImport) {
            $contractorDebtsAvans = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
            $contractorDebtsGU = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');
            $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans + $contractorDebtsGU;
        }

        $providerDebtsAmount = $debts['provider']->total_amount;
        $serviceDebtsAmount = $debts['service']->total_amount;

        $workSalaryDebt = $object->getWorkSalaryDebt();
        $ITRSalaryDebt = $object->getITRSalaryDebt();

        $dolgZakazchikovZaVipolnenieRaboti = $contractsTotal['avanses_acts_left_paid_amount']['RUB'];
        $dolgFactUderjannogoGU = $contractsTotal['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
        $ostatokPoDogovoruSZakazchikom = $contractsTotal['amount']['RUB'] - $contractsTotal['avanses_notwork_left_amount']['RUB'] - $contractsTotal['acts_amount']['RUB'];
        $ostatokNeotrabotannogoAvansa = $contractsTotal['avanses_notwork_left_amount']['RUB'];

        if ($currentRate) {
            $dolgZakazchikovZaVipolnenieRaboti += $contractsTotal['avanses_acts_left_paid_amount']['EUR'] * $currentRate->rate;
            $dolgFactUderjannogoGU += ($contractsTotal['avanses_acts_deposites_amount']['EUR'] - $object->guaranteePayments->where('currency', 'EUR')->sum('amount')) * $currentRate->rate;

            $ostatokPoDogovoruSZakazchikom += ($contractsTotal['amount']['EUR'] * $currentRate->rate);
            $ostatokPoDogovoruSZakazchikom -= ($contractsTotal['avanses_notwork_left_amount']['EUR'] * $currentRate->rate);
            $ostatokPoDogovoruSZakazchikom -= ($contractsTotal['acts_amount']['EUR'] * $currentRate->rate);
            $ostatokNeotrabotannogoAvansa += ($contractsTotal['avanses_notwork_left_amount']['EUR'] * $currentRate->rate);
        }

        $guRUB = $contractsTotal['avanses_non_closes_amount']['RUB'];
        $guEUR = $contractsTotal['avanses_non_closes_amount']['EUR'];

        if ($currentRate) {
            $guRUB = $guRUB + $guEUR * $currentRate->rate;
            $guEUR = 0;
        }

        $objectBalanceRUB = $generalCosts + $balance +
            $dolgZakazchikovZaVipolnenieRaboti +
            $dolgFactUderjannogoGU +
            $contractorDebtsAmount +
            $providerDebtsAmount +
            $serviceDebtsAmount +
            $ITRSalaryDebt +
            $workSalaryDebt;

        $promBalanceRUB = $objectBalanceRUB + $ostatokPoDogovoruSZakazchikom;

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
            'service_debts' => CurrencyExchangeRate::format($serviceDebtsAmount, 'RUB'),
            'workers_debts' => CurrencyExchangeRate::format($workSalaryDebt, 'RUB'),
            'customer_debts_rub' => CurrencyExchangeRate::format($customerDebtRUB, 'RUB'),
            'customer_debts_eur' => CurrencyExchangeRate::format($customerDebtEUR, 'EUR'),
            'customer_acts_debts_rub' => CurrencyExchangeRate::format($customerActDebtRUB, 'RUB'),
            'customer_acts_debts_eur' => CurrencyExchangeRate::format($customerActDebtEUR, 'EUR'),
            'customer_avanses_left_debts_rub' => CurrencyExchangeRate::format($customerAvansLeftDebtRUB, 'RUB'),
            'customer_avanses_left_debts_eur' => CurrencyExchangeRate::format($customerAvansLeftDebtEUR, 'EUR'),
            'customer_acts_gu_debts_rub' => CurrencyExchangeRate::format($customerActGUDebtRUB, 'RUB'),
            'customer_acts_gu_debts_eur' => CurrencyExchangeRate::format($customerActGUDebtEUR, 'EUR'),
            'customer_gu_receive_rub' => CurrencyExchangeRate::format($customerGUReceiveRUB, 'RUB'),
            'customer_gu_receive_eur' => CurrencyExchangeRate::format($customerGUReceiveEUR, 'EUR'),
            'object_balance_rub' => CurrencyExchangeRate::format($objectBalanceRUB, 'RUB'),
            'object_balance_eur' => CurrencyExchangeRate::format(0, 'EUR'),
            'prom_balance_rub' => CurrencyExchangeRate::format($promBalanceRUB, 'RUB'),
            'prom_balance_eur' => CurrencyExchangeRate::format(0, 'EUR'),
            'gu_rub' => CurrencyExchangeRate::format($guRUB, 'RUB'),
            'gu_eur' => CurrencyExchangeRate::format($guEUR, 'EUR'),
            'get_eur_rate' => (bool)$currentRate,
            'dolgZakazchikovZaVipolnenieRaboti_rub' => CurrencyExchangeRate::format($dolgZakazchikovZaVipolnenieRaboti, 'RUB'),
            'dolgFactUderjannogoGU' => CurrencyExchangeRate::format($dolgFactUderjannogoGU, 'RUB'),
            'ostatokNeotrabotannogoAvansa' => CurrencyExchangeRate::format($ostatokNeotrabotannogoAvansa, 'RUB'),
            'ostatokPoDogovoruSZakazchikom' => CurrencyExchangeRate::format($ostatokPoDogovoruSZakazchikom, 'RUB'),
        ];

        return response()->json(compact('info'));
    }
}