<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\FinanceReportHistory;
use App\Models\Loan;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Services\Contract\ContractService;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\FinanceReport\CreditService;
use App\Services\FinanceReport\DepositService;
use App\Services\FinanceReport\LoanService;
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
    private ContractService $contractService;

    public function __construct(
        AccountBalanceService $accountBalanceService,
        CreditService $creditService,
        LoanService $loanService,
        DepositService $depositeService,
        ContractService $contractService
    ) {
        parent::__construct();
        $this->accountBalanceService = $accountBalanceService;
        $this->creditService = $creditService;
        $this->loanService = $loanService;
        $this->depositeService = $depositeService;
        $this->contractService = $contractService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Создание снимка финансового отчета для истории');

        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if ( !$company) {
            Log::channel('custom_imports_log')->debug('[ERROR] Компания ООО "Строй Техно Инженеринг" не найдена в системе');
            return 0;
        }

        $currentDate = Carbon::now();
        $date = $currentDate->format('Y-m-d');

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

        $paymentQuery = Payment::select('object_id', 'amount');
        $objects = BObject::withoutGeneral()
            ->select(['id', 'code', 'name', 'closing_date', 'status_id'])
            ->orderByDesc('code')
            ->orderByDesc('closing_date')
            ->get();

        $years = [];

        foreach ($objects as $object) {
            if ($object->status_id === \App\Models\Status::STATUS_BLOCKED && ! empty($object->closing_date)) {
                $year = \Carbon\Carbon::parse($object->closing_date)->format('Y');

                $years[$year][] = $object;
            } else if ($object->status_id === \App\Models\Status::STATUS_BLOCKED && empty($object->closing_date)) {
                $years['Закрыты, дата не указана'][] = $object;
            } else {
                $years['Активные'][] = $object;
            }
        }

        $summary = [];

        foreach ($years as $year => $objects) {
            $summary[$year] = [
                'payment_total_balance' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'general_costs_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'general_costs_with_balance_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_total_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_avanses_non_closes_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_avanses_left_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_avanses_acts_left_paid_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_avanses_received_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_avanses_acts_paid_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_avanses_notwork_left_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contract_avanses_acts_deposites_amount' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'contractor' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'provider' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'salary_itr' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'salary_work' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'interim_balance' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
                'interim_balance_non_closes' => [
                    'RUB' => 0,
                    'EUR' => 0,
                ],
            ];
        }
        foreach ($years as $year => $objects) {
            foreach ($objects as $object) {
                $total[$year][$object->code]['payment_total_pay'] = (clone $paymentQuery)->where('object_id', $object->id)->where('amount', '<', 0)->sum('amount');
                $total[$year][$object->code]['payment_total_receive'] = (clone $paymentQuery)->where('object_id', $object->id)->sum('amount') - $total[$year][$object->code]['payment_total_pay'];
                $total[$year][$object->code]['payment_total_balance'] = $total[$year][$object->code]['payment_total_pay'] + $total[$year][$object->code]['payment_total_receive'];
                if ($object->code == 288) {
                    $total[$year][$object->code]['general_costs_amount_1'] = $object->generalCosts()->where('is_pinned', false)->sum('amount');
                    $total[$year][$object->code]['general_costs_amount_24'] = $object->generalCosts()->where('is_pinned', true)->sum('amount');
                }

                $total[$year][$object->code]['general_costs_amount'] = $object->generalCosts()->sum('amount');
                // $total[$object->code]['general_costs_with_balance_amount'] = $total[$object->code]['payment_total_balance'] +  $total[$object->code]['general_costs_amount'];


                $summary[$year]['payment_total_balance']['RUB'] += $total[$year][$object->code]['payment_total_balance'];
                $summary[$year]['general_costs_amount']['RUB'] += $total[$year][$object->code]['general_costs_amount'];
                // $summary['general_costs_with_balance_amount']['RUB'] += $total[$object->code]['general_costs_with_balance_amount'];

                $totalInfo = [];
                $contracts = $this->contractService->filterContracts(['object_id' => [$object->id]], $totalInfo);

                $total[$year][$object->code]['contract_total_amount']['RUB'] = $totalInfo['amount']['RUB'];
                $total[$year][$object->code]['contract_total_amount']['EUR'] = $totalInfo['amount']['EUR'];
                $summary[$year]['contract_total_amount']['RUB'] += $total[$year][$object->code]['contract_total_amount']['RUB'];
                $summary[$year]['contract_total_amount']['EUR'] += $total[$year][$object->code]['contract_total_amount']['EUR'];

                $total[$year][$object->code]['contract_avanses_non_closes_amount']['RUB'] = $totalInfo['avanses_non_closes_amount']['RUB'];
                $total[$year][$object->code]['contract_avanses_non_closes_amount']['EUR'] = $totalInfo['avanses_non_closes_amount']['EUR'];
                $summary[$year]['contract_avanses_non_closes_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_non_closes_amount']['RUB'];
                $summary[$year]['contract_avanses_non_closes_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_non_closes_amount']['EUR'];

                $total[$year][$object->code]['contract_avanses_left_amount']['RUB'] = $totalInfo['avanses_left_amount']['RUB'];
                $total[$year][$object->code]['contract_avanses_left_amount']['EUR'] = $totalInfo['avanses_left_amount']['EUR'];
                $summary[$year]['contract_avanses_left_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_left_amount']['RUB'];
                $summary[$year]['contract_avanses_left_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_left_amount']['EUR'];

                $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] = $totalInfo['avanses_acts_left_paid_amount']['RUB'];
                $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] = $totalInfo['avanses_acts_left_paid_amount']['EUR'];
                $summary[$year]['contract_avanses_acts_left_paid_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['RUB'];
                $summary[$year]['contract_avanses_acts_left_paid_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['EUR'];

                $total[$year][$object->code]['contract_avanses_received_amount']['RUB'] = $totalInfo['avanses_received_amount']['RUB'];
                $total[$year][$object->code]['contract_avanses_received_amount']['EUR'] = $totalInfo['avanses_received_amount']['EUR'];
                $summary[$year]['contract_avanses_received_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_received_amount']['RUB'];
                $summary[$year]['contract_avanses_received_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_received_amount']['EUR'];

                $total[$year][$object->code]['contract_avanses_acts_paid_amount']['RUB'] = $totalInfo['avanses_acts_paid_amount']['RUB'];
                $total[$year][$object->code]['contract_avanses_acts_paid_amount']['EUR'] = $totalInfo['avanses_acts_paid_amount']['EUR'];
                $summary[$year]['contract_avanses_acts_paid_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_acts_paid_amount']['RUB'];
                $summary[$year]['contract_avanses_acts_paid_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_acts_paid_amount']['EUR'];

                $total[$year][$object->code]['contract_avanses_notwork_left_amount']['RUB'] = $totalInfo['avanses_notwork_left_amount']['RUB'];
                $total[$year][$object->code]['contract_avanses_notwork_left_amount']['EUR'] = $totalInfo['avanses_notwork_left_amount']['EUR'];
                $summary[$year]['contract_avanses_notwork_left_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_notwork_left_amount']['RUB'];
                $summary[$year]['contract_avanses_notwork_left_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_notwork_left_amount']['EUR'];

                $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['RUB'] = $totalInfo['avanses_acts_deposites_amount']['RUB'];
                $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['EUR'] = $totalInfo['avanses_acts_deposites_amount']['EUR'];
                $summary[$year]['contract_avanses_acts_deposites_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['RUB'];
                $summary[$year]['contract_avanses_acts_deposites_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['EUR'];

                $total[$year][$object->code]['contractor']['RUB'] = $object->getContractorDebtsAmount();
                $total[$year][$object->code]['provider']['RUB'] = $object->getProviderDebtsAmount();

                $ITRSalaryObject = \App\Models\CRM\ItrSalary::where('kod', 'LIKE', '%' . $object->code. '%')->get();
                $workSalaryObjectAmount = \App\Models\CRM\SalaryDebt::where('object_code', 'LIKE', '%' . $object->code. '%')->sum('amount');

                $total[$year][$object->code]['salary_itr']['RUB'] = $ITRSalaryObject->sum('paid') - $ITRSalaryObject->sum('total');
                $total[$year][$object->code]['salary_work']['RUB'] = $workSalaryObjectAmount;

                $total[$year][$object->code]['interim_balance']['RUB'] =
                    $total[$year][$object->code]['payment_total_balance'] +
                    $total[$year][$object->code]['general_costs_amount'] +
                    $total[$year][$object->code]['contract_avanses_left_amount']['RUB'] +
                    $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] +
                    $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['RUB'] +
                    $total[$year][$object->code]['contractor']['RUB'] +
                    $total[$year][$object->code]['provider']['RUB'] +
                    $total[$year][$object->code]['salary_itr']['RUB'] +
                    $total[$year][$object->code]['salary_work']['RUB'];

                $total[$year][$object->code]['interim_balance']['EUR'] =
                    $total[$year][$object->code]['contract_avanses_left_amount']['EUR'] +
                    $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] +
                    $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['EUR'];

                $total[$year][$object->code]['interim_balance_non_closes']['RUB'] =
                    $total[$year][$object->code]['interim_balance']['RUB'] +
                    $total[$year][$object->code]['contract_avanses_non_closes_amount']['RUB'] -
                    $total[$year][$object->code]['contract_avanses_left_amount']['RUB'];

                $total[$year][$object->code]['interim_balance_non_closes']['EUR'] =
                    $total[$year][$object->code]['interim_balance']['EUR'] +
                    $total[$year][$object->code]['contract_avanses_non_closes_amount']['EUR'] -
                    $total[$year][$object->code]['contract_avanses_left_amount']['EUR'];

                $summary[$year]['contractor']['RUB'] += $total[$year][$object->code]['contractor']['RUB'];
                $summary[$year]['provider']['RUB'] += $total[$year][$object->code]['provider']['RUB'];
                $summary[$year]['salary_itr']['RUB'] += $total[$year][$object->code]['salary_itr']['RUB'];
                $summary[$year]['salary_work']['RUB'] += $total[$year][$object->code]['salary_work']['RUB'];
                $summary[$year]['interim_balance']['RUB'] += $total[$year][$object->code]['interim_balance']['RUB'];
                $summary[$year]['interim_balance']['EUR'] += $total[$year][$object->code]['interim_balance']['EUR'];
                $summary[$year]['interim_balance_non_closes']['RUB'] += $total[$year][$object->code]['interim_balance_non_closes']['RUB'];
                $summary[$year]['interim_balance_non_closes']['EUR'] += $total[$year][$object->code]['interim_balance_non_closes']['EUR'];
            }
        }

        $infos = [
            'Текущее сальдо' => 'payment_total_balance',
            'Общие затраты' => 'general_costs_amount',
            'Промежуточный баланс с текущими долгами и общими расходами компании' => 'interim_balance',
            'Общая сумма договоров' => 'contract_total_amount',
            'Остаток денег к получ. с учётом ГУ' => 'contract_avanses_non_closes_amount',
            'PROM BALANS +  NE ZAKRITI DOGOVOR' => 'interim_balance_non_closes',
            'Сумма аванса к получению' => 'contract_avanses_left_amount',
            'Долг подписанных актов' => 'contract_avanses_acts_left_paid_amount',
            'Всего оплачено авансов' => 'contract_avanses_received_amount',
            'Всего оплачено по актам' => 'contract_avanses_acts_paid_amount',
            'Не закрытый аванс' => 'contract_avanses_notwork_left_amount',
            'Долг гарантийного удержания' => 'contract_avanses_acts_deposites_amount',
            'Долг подрядчикам' => 'contractor',
            'Долг за материалы' => 'provider',
            'Долг на зарплаты ИТР' => 'salary_itr',
            'Долг на зарплаты рабочим' => 'salary_work',
        ];

        $objects = json_encode(compact('total', 'infos', 'summary', 'objects', 'years'));

        $financeReportHistory = FinanceReportHistory::where('date', $date)->first();

        // В этом месте можно будет регулировать, сохранять ли каждый раз новую копию, или перезаписывать существующую на один и тот же день
        if ($financeReportHistory) {
            $financeReportHistory->update(
                compact('balances', 'credits', 'loans', 'deposits', 'objects')
            );
        } else {
            FinanceReportHistory::create(
                compact('date', 'balances', 'credits', 'loans', 'deposits', 'objects')
            );
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Создание прошло успешно');

        return 0;
    }
}
