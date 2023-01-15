<?php

namespace App\Http\Controllers\Pivot\Balance;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\PaymentImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BalanceController extends Controller
{
    public function index(Request $request): View
    {
        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if ( !$company) {
            abort(404);
        }

        $period = $request->get('balance_date', Carbon::now()->format('d.m.Y') . ' - ' . Carbon::now()->format('d.m.Y'));
        $period = explode(' - ', $period);

        $banks = Bank::getBanks();
        $exceptBanks = [5]; // Росбанк для СТИ не нужно показывать

        $currencies = Currency::getCurrencies();
        unset($currencies['USD']);

        $svods = [
            'incoming_balance' => 'Остаток на начало дня',
            'amount_receive' => 'Всего приходов',
            'amount_pay' => 'Всего расходов',
            'outgoing_balance' => 'Остаток на конец дня',
        ];

        $pivot = [
            'entries' => [],
            'banks' => [],
        ];

        foreach ($banks as $bankId => $bankName) {
            if (in_array($bankId, $exceptBanks)) {
                continue;
            }

            foreach ($currencies as $currency) {

                $paymentImportStart = PaymentImport::where('company_id', $company->id)
                    ->where('date', Carbon::parse($period[0]))
                    ->where('bank_id', $bankId)
                    ->where('currency', $currency)
                    ->orderBy('date', 'desc')
                    ->first();

                $paymentImportEnd = PaymentImport::where('company_id', $company->id)
                    ->where('date', Carbon::parse($period[1]))
                    ->where('bank_id', $bankId)
                    ->where('currency', $currency)
                    ->orderBy('date', 'desc')
                    ->first();

                foreach ($svods as $svodField => $svodName) {
                    if (! isset($pivot['entries']['start'][$svodName]['total'][$currency])) {
                        $pivot['entries']['start'][$svodName]['total'][$currency] = 0;
                    }
                    if (! isset($pivot['entries']['end'][$svodName]['total'][$currency])) {
                        $pivot['entries']['end'][$svodName]['total'][$currency] = 0;
                    }

                    $amountStart = $paymentImportStart->{$svodField} ?? 0;
                    $amountEnd = $paymentImportEnd->{$svodField} ?? 0;

                    if ($svodField === 'outgoing_balance') {
                        $amountStart = $pivot['entries']['start']['Остаток на начало дня']['banks'][$bankId][$currency] +
                            $pivot['entries']['start']['Всего приходов']['banks'][$bankId][$currency] +
                            $pivot['entries']['start']['Всего расходов']['banks'][$bankId][$currency];

                        $amountEnd = $pivot['entries']['end']['Остаток на начало дня']['banks'][$bankId][$currency] +
                            $pivot['entries']['end']['Всего приходов']['banks'][$bankId][$currency] +
                            $pivot['entries']['end']['Всего расходов']['banks'][$bankId][$currency];
                    }

                    $pivot['entries']['start'][$svodName]['banks'][$bankId][$currency] = $amountStart;
                    $pivot['entries']['start'][$svodName]['total'][$currency] += $amountStart;

                    $pivot['entries']['end'][$svodName]['banks'][$bankId][$currency] = $amountEnd;
                    $pivot['entries']['end'][$svodName]['total'][$currency] += $amountEnd;
                }

                if (! isset($pivot['entries']['total']['amount_receive'][$currency])) {
                    $pivot['entries']['total']['amount_receive'][$currency] = 0;
                }

                if (! isset($pivot['entries']['total']['amount_pay'][$currency])) {
                    $pivot['entries']['total']['amount_pay'][$currency] = 0;
                }

                $pivot['entries']['total']['amount_receive'][$currency] += PaymentImport::where('company_id', $company->id)
                    ->whereBetween('date', [Carbon::parse($period[0]), Carbon::parse($period[1])])
                    ->where('bank_id', $bankId)
                    ->where('currency', $currency)
                    ->sum('amount_receive');

                $pivot['entries']['total']['amount_pay'][$currency] += PaymentImport::where('company_id', $company->id)
                    ->whereBetween('date', [Carbon::parse($period[0]), Carbon::parse($period[1])])
                    ->where('bank_id', $bankId)
                    ->where('currency', $currency)
                    ->sum('amount_pay');
            }

            $pivot['banks'][] = ['name' => $bankName, 'logo' => Bank::getBankLogo($bankId)];
        }

        $allPaymentImportsIds = PaymentImport::where('company_id', $company->id)
            ->whereBetween('date', [Carbon::parse($period[0]), Carbon::parse($period[1])])
            ->whereNotNull('bank_id')
            ->where('currency', 'RUB')
            ->pluck('id')->toArray();

        $groupedReceivedPayments = Payment::whereIn('import_id', $allPaymentImportsIds)->where('amount', '>', 0)->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M y');
        });

        $pivot['charts']['receive']['values'] = [];
        $pivot['charts']['receive']['months'] = [];

        foreach ($groupedReceivedPayments as $date => $payments) {
            $amount = $payments->sum('amount');
            $pivot['charts']['receive']['values'][] = $amount;
            $pivot['charts']['receive']['months'][] = $date;
        }

        $pivot['charts']['receive']['max_values'] = max($pivot['charts']['receive']['values']);
        $pivot['charts']['receive']['min_values'] = min($pivot['charts']['receive']['values']);

        $groupedPayedPayments = Payment::whereIn('import_id', $allPaymentImportsIds)->where('amount', '<', 0)->get()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M y');
        });

        $pivot['charts']['pay']['values'] = [];
        $pivot['charts']['pay']['months'] = [];

        foreach ($groupedPayedPayments as $date => $payments) {
            $amount = $payments->sum('amount');
            $pivot['charts']['pay']['values'][] = $amount;
            $pivot['charts']['pay']['months'][] = $date;
        }

        $pivot['charts']['pay']['max_values'] = max($pivot['charts']['pay']['values']);;
        $pivot['charts']['pay']['min_values'] = min($pivot['charts']['pay']['values']);;

        return view('pivots.balances.index', compact('pivot', 'period'));
    }
}
