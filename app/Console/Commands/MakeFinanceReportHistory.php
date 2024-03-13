<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\FinanceReport;
use App\Models\FinanceReportHistory;
use App\Models\Loan;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Services\Contract\ContractService;
use App\Services\CRONProcessService;
use App\Services\CurrencyExchangeRateService;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\FinanceReport\CreditService;
use App\Services\FinanceReport\DepositService;
use App\Services\FinanceReport\LoanService;
use App\Services\ObjectPlanPaymentService;
use App\Services\PivotObjectDebtService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MakeFinanceReportHistory extends Command
{
    protected $signature = 'oms:make-finance-report-history';

    protected $description = 'Создает снимок финансового отчета для истории';

    private AccountBalanceService $accountBalanceService;
    private CreditService $creditService;
    private LoanService $loanService;
    private DepositService $depositeService;
    private PivotObjectDebtService $pivotObjectDebtService;
    private CurrencyExchangeRateService $currencyExchangeService;
    private ContractService $contractService;
    private ObjectPlanPaymentService $objectPlanPaymentService;

    public function __construct(
        AccountBalanceService $accountBalanceService,
        CreditService $creditService,
        LoanService $loanService,
        DepositService $depositeService,
        CRONProcessService $CRONProcessService,
        PivotObjectDebtService $pivotObjectDebtService,
        CurrencyExchangeRateService $currencyExchangeService,
        ContractService $contractService,
        ObjectPlanPaymentService $objectPlanPaymentService,
    ) {
        parent::__construct();
        $this->accountBalanceService = $accountBalanceService;
        $this->creditService = $creditService;
        $this->loanService = $loanService;
        $this->depositeService = $depositeService;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
        $this->currencyExchangeService = $currencyExchangeService;
        $this->contractService = $contractService;
        $this->objectPlanPaymentService = $objectPlanPaymentService;
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно каждые 10 минут'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Создание снимка финансового отчета для истории');

        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if ( !$company) {
            $errorMessage = '[ERROR] Компания ООО "Строй Техно Инженеринг" не найдена в системе';
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        try {
            $currentDate = Carbon::now();

            // Балансы
            $balancesFromService = $this->accountBalanceService->getBalances($currentDate, $company);
            $dateLastStatement = PaymentImport::orderByDesc('date')->first()->created_at->format('d.m.Y H:i');
            $balances = json_encode(['balances' => $balancesFromService, 'dateLastStatement' => $dateLastStatement]);

            // Кредиты
            $creditsFromService = $this->creditService->getCredits($currentDate, $company);
            $totalCreditAmount = $this->creditService->getTotalCreditAmount($currentDate, $company);
            $creditsLastUpdateDate = Loan::where('type_id', Loan::TYPE_CREDIT)->latest('updated_at')->first()->updated_at->format('d.m.Y');
            $credits = json_encode(['credits' => $creditsFromService, 'totalCreditAmount' => $totalCreditAmount, 'creditsLastUpdateDate' => $creditsLastUpdateDate]);

            // Долги
            $loansFromService = $this->loanService->getLoans($currentDate, $company);
            $totalLoanAmount = $loansFromService->sum('amount');
            $loansLastUpdateDate = Loan::where('type_id', Loan::TYPE_LOAN)->latest('updated_at')->first()->updated_at->format('d.m.Y');
            $loans = json_encode(['loans' => $loansFromService, 'totalLoanAmount' => $totalLoanAmount, 'loansLastUpdateDate' => $loansLastUpdateDate]);

            // Депозиты
            $depositsFromService = $this->depositeService->getDeposites($currentDate, $company);
            $totalDepositsAmount = $this->depositeService->getDepositesTotalAmount($currentDate, $company);
            $deposits = json_encode(['deposits' => $depositsFromService, 'totalDepositsAmount' => $totalDepositsAmount]);

            $total = [];

            $objects = BObject::withoutGeneral()
                ->select(['id', 'code', 'name', 'closing_date', 'status_id'])
                ->orderByDesc('code')
                ->orderByDesc('closing_date')
                ->get();

            $years = [];

            foreach ($objects as $object) {
                if ($object->status_id === Status::STATUS_BLOCKED && !empty($object->closing_date)) {
//                    $year = \Carbon\Carbon::parse($object->closing_date)->format('Y');
//
//                    $years[$year][] = $object;
                    $years['Закрытые'][] = $object;
                } else if ($object->status_id === Status::STATUS_BLOCKED && empty($object->closing_date)) {
//                    $years['Закрыты, дата не указана'][] = $object;
                    $years['Закрытые'][] = $object;
                } else {
                    if ($object->status_id !== Status::STATUS_DELETED) {
                        $years['Активные'][] = $object;
                    }
                }
            }

            $summary = [];

            foreach ($years as $year => $objects) {
                $fields = FinanceReport::getInfoFields();
                foreach ($fields as $field) {
                    $summary[$year][$field] = 0;
                }
            }

            $paymentQuery = Payment::select('object_id', 'amount');

            foreach ($years as $year => $objects) {
                foreach ($objects as $object) {

                    $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

                    $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
                    $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
                    $object->total_balance = $object->total_pay + $object->total_receive;
                    $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');
                    $object->general_balance = $object->total_with_general_balance - $object->total_balance;

                    $debts = $this->pivotObjectDebtService->getPivotDebtForObject($object->id);
                    $serviceDebtsAmount = $debts['service']->total_amount;
                    $contractorDebtsAmount = $debts['contractor']->total_amount;
                    $contractorGuaranteeDebtsAmount = 0;

                    $debtObjectImport = DebtImport::where('type_id',DebtImport::TYPE_OBJECT)->latest('date')->first();
                    $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

                    if ($objectExistInObjectImport) {
                        $contractorDebtsAvans = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('avans');
                        $contractorDebtsGU = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->sum('guarantee');
                        $contractorDebtsAmount = $contractorDebtsAmount + $contractorDebtsAvans + $contractorDebtsGU;
                        $contractorGuaranteeDebtsAmount = $contractorDebtsGU;
                    }

                    $providerDebtsAmount = $debts['provider']->total_amount;
                    $ITRSalaryDebt = $object->getITRSalaryDebt();
                    $workSalaryDebt = $object->getWorkSalaryDebt();
                    $workSalaryDebtDetails = $object->getWorkSalaryDebtDetails();
                    $customerDebtInfo = [];
                    $this->contractService->filterContracts(['object_id' => [$object->id]], $customerDebtInfo);
//                $customerDebt = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'] + $customerDebtInfo['avanses_left_amount']['RUB'] + $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

                    $contractsTotalAmount = $customerDebtInfo['amount']['RUB'];

                    $dolgZakazchikovZaVipolnenieRaboti = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'];
                    $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

                    // старая версия
//                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_notwork_left_amount']['RUB'] - $customerDebtInfo['acts_amount']['RUB'];

                    //новая версия
                    $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_received_amount']['RUB'] - $customerDebtInfo['avanses_acts_paid_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

                    $ostatokNeotrabotannogoAvansa = $customerDebtInfo['avanses_notwork_left_amount']['RUB'];

                    $writeoffs = $object->writeoffs->sum('amount');

                    $date = now();
                    $EURExchangeRate = $this->currencyExchangeService->getExchangeRate($date->format('Y-m-d'), 'EUR');
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
                            $ostatokPoDogovoruSZakazchikom -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount') * $EURExchangeRate->rate;
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

                    if (! empty($object->closing_date) && $object->status_id === Status::STATUS_BLOCKED) {
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

                    $generalBalanceToReceivePercentage = $object->total_receive == 0 ? 0 : $object->general_balance / $object->total_receive * 100;

                    // ------ Расчет прогнозируемых затрат

                    if ($object->planPayments->count() === 0) {
                        $this->objectPlanPaymentService->makeDefaultPaymentsForObject($object->id);
                    }

                    $prognozTotal = 0;
                    $prognozFields = FinanceReport::getPrognozFields();
                    foreach ($prognozFields as $field) {
                        $prognozAmount = -$ostatokPoDogovoruSZakazchikom * FinanceReport::getPercentForField($field);

                        if ($field === 'prognoz_consalting' && $object->code !== '361') {
                            $prognozAmount = 0;
                        }

                        if ($field === 'prognoz_podryad') {
                            $prognozAmount = $debts['contractor']->balance_contract;
                        }

                        $total[$year][$object->code][$field] = $prognozAmount;
                        $prognozTotal += $prognozAmount;

                        foreach ($object->planPayments as $planPayment) {
                            if ($field === $planPayment->field && $planPayment->isAutoCalculation()) {
                                $planPayment->update([
                                    'amount' => $prognozAmount
                                ]);
                                break;
                            }
                        }
                    }

                    $prognozBalance = $objectBalance + $ostatokPoDogovoruSZakazchikom - $dolgFactUderjannogoGU + $prognozTotal;

                    //----------------------------------------

                    $total[$year][$object->code]['pay'] = $object->total_pay;
                    $total[$year][$object->code]['receive'] = $object->total_receive;
                    $total[$year][$object->code]['balance'] = $object->total_balance;
                    $total[$year][$object->code]['general_balance'] = $object->general_balance;
                    $total[$year][$object->code]['general_balance_to_receive_percentage'] = $generalBalanceToReceivePercentage;
                    $total[$year][$object->code]['balance_with_general_balance'] = $object->total_with_general_balance;

                    $total[$year][$object->code]['contractor_debt'] = $contractorDebtsAmount - $contractorGuaranteeDebtsAmount;
                    $total[$year][$object->code]['contractor_debt_gu'] = $contractorGuaranteeDebtsAmount;
                    $total[$year][$object->code]['provider_debt'] = $providerDebtsAmount;
                    $total[$year][$object->code]['service_debt'] = $serviceDebtsAmount;
//                    $total[$year][$object->code]['itr_salary_debt'] = $ITRSalaryDebt;
                    $total[$year][$object->code]['workers_salary_debt'] = $workSalaryDebt;
                    $total[$year][$object->code]['dolgZakazchikovZaVipolnenieRaboti'] = $dolgZakazchikovZaVipolnenieRaboti;
                    $total[$year][$object->code]['dolgFactUderjannogoGU'] = $dolgFactUderjannogoGU;
                    $total[$year][$object->code]['objectBalance'] = $objectBalance;
                    $total[$year][$object->code]['contractsTotalAmount'] = $contractsTotalAmount;
                    $total[$year][$object->code]['ostatokNeotrabotannogoAvansa'] = $ostatokNeotrabotannogoAvansa;
                    $total[$year][$object->code]['ostatokPoDogovoruSZakazchikom'] = $ostatokPoDogovoruSZakazchikom;
                    $total[$year][$object->code]['prognoz_total'] = $prognozTotal;
                    $total[$year][$object->code]['prognozBalance'] = $prognozBalance;

                    foreach ($total[$year][$object->code] as $key => $value) {
                        $summary[$year][$key] += $value;
                    }
                }

                $summary[$year]['general_balance_to_receive_percentage'] = $summary[$year]['receive'] == 0 ? 0 : $summary[$year]['general_balance'] / $summary[$year]['receive'] * 100;
            }

        } catch (\Exception $e) {
            $errorMessage = '[ERROR] Ошибка в вычислениях: ' . $e->getMessage();
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        $infos = FinanceReport::getInfoFields();

        $objects_new = json_encode(compact('total', 'infos', 'summary', 'objects', 'years'));
        $objects = json_encode([]);

        $date = Carbon::now()->format('Y-m-d');
        $financeReportHistory = FinanceReportHistory::where('date', $date)->first();
        // В этом месте можно будет регулировать, сохранять ли каждый раз новую копию, или перезаписывать существующую на один и тот же день
        if ($financeReportHistory) {
            $financeReportHistory->update(
                compact('balances', 'credits', 'loans', 'deposits', 'objects', 'objects_new')
            );
        } else {
            FinanceReportHistory::create(
                compact('date', 'balances', 'credits', 'loans', 'deposits', 'objects', 'objects_new')
            );
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Создание прошло успешно');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
