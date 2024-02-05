<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ContractService;
use App\Services\CurrencyExchangeRateService;
use App\Services\PivotObjectDebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    private PivotObjectDebtService $pivotObjectDebtService;
    private CurrencyExchangeRateService $currencyExchangeRateService;
    private ContractService $contractService;

    public function __construct(
        PivotObjectDebtService $pivotObjectDebtService,
        CurrencyExchangeRateService $currencyExchangeRateService,
        ContractService $contractService,
    ) {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
        $this->currencyExchangeRateService = $currencyExchangeRateService;
        $this->contractService = $contractService;
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

        $info = [];
        $objects = BObject::active()->orderBy('code', 'desc')->get();

        foreach ($objects as $object) {
            $paymentQuery = Payment::select('object_id', 'amount');
            $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

            $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
            $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
            $object->total_balance = $object->total_pay + $object->total_receive;
            $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');
            if ($object->code === '288') {
                $object->general_balance_1 = $object->generalCosts()->where('is_pinned', false)->sum('amount');
                $object->general_balance_24 = $object->generalCosts()->where('is_pinned', true)->sum('amount');
            }

            $debts = $this->pivotObjectDebtService->getPivotDebtForObject($object->id);
            $serviceDebtsAmount = $debts['service']->total_amount;
            $contractorDebtsAmount = $debts['contractor']->total_amount;

            $debtObjectImport = \App\Models\Debt\DebtImport::where('type_id', \App\Models\Debt\DebtImport::TYPE_OBJECT)->latest('date')->first();
            $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

            if ($objectExistInObjectImport) {
                $contractorDebtsAvans = \App\Models\Debt\Debt::where('import_id', $debtObjectImport->id)->where('type_id', \App\Models\Debt\Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
                $contractorDebtsGU = \App\Models\Debt\Debt::where('import_id', $debtObjectImport->id)->where('type_id', \App\Models\Debt\Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');
                $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans + $contractorDebtsGU;
            }

            $providerDebtsAmount = $debts['provider']->total_amount;
            $ITRSalaryDebt = $object->getITRSalaryDebt();
            $workSalaryDebt = $object->getWorkSalaryDebt();
            $customerDebtInfo = [];
            $this->contractService->filterContracts(['object_id' => [$object->id]], $customerDebtInfo);
//                $customerDebt = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'] + $customerDebtInfo['avanses_left_amount']['RUB'] + $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

            $contractsTotalAmount = $customerDebtInfo['amount']['RUB'];

            $dolgZakazchikovZaVipolnenieRaboti = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'];
            $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

            // старая версия
//                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_notwork_left_amount']['RUB'] - $customerDebtInfo['acts_amount']['RUB'];

            //новая версия
            if ($object->code === '346') {
                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_received_amount']['RUB'] - $customerDebtInfo['avanses_acts_paid_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
            } else {
                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_received_amount']['RUB'] - $customerDebtInfo['avanses_acts_paid_amount']['RUB'];
            }

            $ostatokNeotrabotannogoAvansa = $customerDebtInfo['avanses_notwork_left_amount']['RUB'];

            $writeoffs = $object->writeoffs->sum('amount');

            $date = now();
            $EURExchangeRate = $this->currencyExchangeRateService->getExchangeRate($date->format('Y-m-d'), 'EUR');
            if ($EURExchangeRate) {
                $dolgZakazchikovZaVipolnenieRaboti += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
                $dolgFactUderjannogoGU += ($customerDebtInfo['avanses_acts_deposites_amount']['EUR'] - $object->guaranteePayments->where('currency', 'EUR')->sum('amount')) * $EURExchangeRate->rate;
                $ostatokNeotrabotannogoAvansa += ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);

                // старая версия
//                    $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['acts_amount']['EUR'] * $EURExchangeRate->rate);

                //новая версия


                if ($object->code === '346') {
                    $diff = $customerDebtInfo['amount']['EUR'] - $customerDebtInfo['avanses_received_amount']['EUR'] - $customerDebtInfo['avanses_acts_paid_amount']['EUR'];
                    $ostatokPoDogovoruSZakazchikom += $diff * $EURExchangeRate->rate;
                    $ostatokPoDogovoruSZakazchikom -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount') * $EURExchangeRate->rate;
                } else {
                    $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_received_amount']['EUR'] * $EURExchangeRate->rate);
                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_acts_paid_amount']['EUR'] * $EURExchangeRate->rate);
                }

//                    $customerDebt += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_left_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_acts_deposites_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount')  * $EURExchangeRate->rate;

                $contractsTotalAmount += $customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate;
            }

            if ($object->code === '288') {
                $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'];
            }

            if (! empty($object->closing_date) && $object->status_id === \App\Models\Status::STATUS_BLOCKED) {
                $ostatokPoDogovoruSZakazchikom = $dolgFactUderjannogoGU;
            }

            $objectBalance = $object->total_with_general_balance +
                $dolgZakazchikovZaVipolnenieRaboti +
                $dolgFactUderjannogoGU +
                $contractorDebtsAmount +
                $providerDebtsAmount +
                $serviceDebtsAmount +
                $ITRSalaryDebt +
                $workSalaryDebt +
                $writeoffs;

            $info[] = [
                'id' => $object->id,
                'title' => $object->getName(),
                'balance' => CurrencyExchangeRate::format($objectBalance, 'RUB'),
            ];
        }

        return response()->json(compact('info'));
    }
}
