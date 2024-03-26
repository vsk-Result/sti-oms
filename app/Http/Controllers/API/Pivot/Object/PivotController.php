<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
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
//        if (! $request->has('verify_hash')) {
//            abort(403);
//            return response()->json([], 403);
//        }
//
//        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
//            abort(403);
//            return response()->json([], 403);
//        }

        if (empty($request->object_id)) {
            abort(404);
            return response()->json([], 404);
        }

        $object = BObject::find($request->object_id);

        if (! $object) {
            abort(404);
            return response()->json([], 404);
        }

        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $info = [];

        foreach ($years as $year => $objects) {
            foreach ($objects as $o) {
                if ($o->id === $object->id) {
                    $info = (array) $total->{$year}->{$object->code};
                    break;
                }
            }
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
            'contractors_debts_without_gu' => CurrencyExchangeRate::format($contractorDebtsAmount - $contractorDebtsGU, 'RUB'),
            'contractors_debts_gu' => CurrencyExchangeRate::format($contractorDebtsGU, 'RUB'),
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
            'prom_balance_rub' => CurrencyExchangeRate::format($prognozBalance, 'RUB'),
            'prom_balance_eur' => CurrencyExchangeRate::format(0, 'EUR'),
            'gu_rub' => CurrencyExchangeRate::format($guRUB, 'RUB'),
            'gu_eur' => CurrencyExchangeRate::format($guEUR, 'EUR'),
            'get_eur_rate' => (bool)$currentRate,
            'dolgZakazchikovZaVipolnenieRaboti_rub' => CurrencyExchangeRate::format($dolgZakazchikovZaVipolnenieRaboti, 'RUB'),
            'dolgFactUderjannogoGU' => CurrencyExchangeRate::format($dolgFactUderjannogoGU, 'RUB'),
            'ostatokNeotrabotannogoAvansa' => CurrencyExchangeRate::format($ostatokNeotrabotannogoAvansa, 'RUB'),
            'ostatokPoDogovoruSZakazchikom' => CurrencyExchangeRate::format($ostatokPoDogovoruSZakazchikom, 'RUB'),
            'prognoz_total' => CurrencyExchangeRate::format($prognozTotal, 'RUB'),
            'prognoz_zp_worker' => CurrencyExchangeRate::format($prognozZpWorker, 'RUB'),
            'prognoz_zp_itr' => CurrencyExchangeRate::format($prognozZpITR, 'RUB'),
            'prognoz_material' => CurrencyExchangeRate::format($prognozMaterial, 'RUB'),
            'prognoz_podryad' => CurrencyExchangeRate::format($prognozPodryad, 'RUB'),
            'prognoz_general' => CurrencyExchangeRate::format($prognozGeneral, 'RUB'),
            'prognoz_service' => CurrencyExchangeRate::format($prognozService, 'RUB'),
            'prognoz_consalting' => CurrencyExchangeRate::format($prognozConsalting, 'RUB'),
        ];

        return response()->json(compact('info'));
    }
}