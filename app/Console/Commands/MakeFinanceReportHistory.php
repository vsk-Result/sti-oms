<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Exports\Finance\FinanceReport\Export;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\FinanceReport;
use App\Models\FinanceReportHistory;
use App\Models\Loan;
use App\Models\Object\BObject;
use App\Models\Object\PlanPayment;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\PivotObjectDebt;
use App\Models\Status;
use App\Models\TaxPlanItem;
use App\Services\Contract\ActService;
use App\Services\Contract\ContractService;
use App\Services\CurrencyExchangeRateService;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\FinanceReport\CreditService;
use App\Services\FinanceReport\DepositService;
use App\Services\FinanceReport\LoanService;
use App\Services\GeneralReportService;
use App\Services\ObjectPlanPaymentService;
use App\Services\PivotObjectDebtService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Maatwebsite\Excel\Facades\Excel;

class MakeFinanceReportHistory extends HandledCommand
{
    protected $signature = 'oms:make-finance-report-history';

    protected $description = 'Создает снимок финансового отчета для истории';

    protected string $period = 'Ежедневно каждые 10 минут';

    public function __construct(
        private AccountBalanceService $accountBalanceService,
        private CreditService $creditService,
        private LoanService $loanService,
        private DepositService $depositeService,
        private PivotObjectDebtService $pivotObjectDebtService,
        private CurrencyExchangeRateService $currencyExchangeService,
        private ContractService $contractService,
        private ObjectPlanPaymentService $objectPlanPaymentService,
        private GeneralReportService $generalReportService,
        private ActService $actService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        ini_set('memory_limit', '-1');

        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $pivotInfoFromGromisoft = [];

        $objectsFromGromisoft = [
            '367' => '367. БЦ Легенда Цветного - Vesper Office',
            '363' => 'Аэропорт Елизово Камчатка',
            '361' => 'Гостиница Кемерово (первый этап, второй этап)',
            '358' => 'Гостиница Меркурий Завидово',
            '360' => 'Тинькофф',
            '376' => 'Сбербанк',
            '372' => '372. Офис Продаж Vesper',
            '369' => 'МОНОСПЕЙС',
            '373' => 'ДЕТСКИЙ ЦЕНТР МАГНИТОГОРСК',
            '374' => '374. Офис компании GRAND LINE - Обнинск',
            '365' => '365. Флагманский офис продаж AEROFLOT',
            '380' => 'ЛЕВЕНСОН',
            '378' => '378. Офис компании Velesstroy',
        ];

        $hash = md5(date('dmY'));
        $endpoint = "https://sti.gromitsoft.space/api/v1/projects?sti_token=sti_" . $hash;
        $client = new Client();

        try {
            $response = $client->request('POST', $endpoint);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $pivotInfoFromGromisoft = json_decode($response->getBody(), true);
            }
        } catch (\Throwable $e) {
            $this->sendErrorMessage('Не удалось загрузить данные из https://sti.gromitsoft.space/api/v1/projects: ' . $e->getMessage());
        }

        $company = Company::getSTI();

        if ( !$company) {
            $this->sendErrorMessage('Компания ООО "Строй Техно Инженеринг" не найдена в системе');
            $this->endProcess();
            return 0;
        }

        try {
            $currentDate = Carbon::now();
            $object27_1 = BObject::where('code', '27.1')->first();

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

            $objects = BObject::select(['id', 'code', 'name', 'closing_date', 'status_id'])
                ->orderByDesc('code')
                ->orderByDesc('closing_date')
                ->get();

            $generalObjectCodes = BObject::getCodesWithoutWorktype();
            $hideObjectCodes = ['362', '368'];
            $hidePrognozObjectCodes = ['346', '353'];

            $years = [];

            foreach ($objects as $object) {
                if ($object->code === '000') {
                    continue;
                }
                if (in_array($object->code, $hideObjectCodes)) {
                    $years['Не отображать'][] = $object;
                    continue;
                }
                if (in_array($object->code, $generalObjectCodes)) {
                    $years['Общие'][] = $object;
                    continue;
                }
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
                    } else {
                        $years['Удаленные'][] = $object;
                    }
                }
            }

            $closedObject = BObject::where('code', '000')->first();
            if ($closedObject) {
                $years['Активные'][] = $closedObject;
            }

            $summary = [];

            foreach ($years as $year => $objects) {
                $fields = FinanceReport::getInfoFields();
                foreach ($fields as $field) {
                    $summary[$year][$field] = 0;
                }
            }

            $paymentQuery = Payment::select('object_id', 'amount', 'amount_without_nds', 'payment_type_id');

            foreach ($years as $year => $objects) {
                $sGeneralTotal = 0;
                $sReceiveTotal = 0;
                $sContractsTotalAmount = 0;
                $sActsTotalAmount = 0;
                $sReceiveFromCustomers = 0;

                foreach ($objects as $object) {
                    $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();
                    $pay = $objectPayments->where('amount', '<', 0)->sum('amount');
                    $receive = $objectPayments->sum('amount') - $pay;

                    $sReceiveTotal += $receive;
                    $sGeneralTotal += $object->generalCosts()->sum('amount') + $object->transferService()->sum('amount');
                }

                $avgPercent = (float)number_format($sReceiveTotal == 0 ? 0 : $sGeneralTotal / $sReceiveTotal * 100, 2);

                foreach ($objects as $object) {

                    $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

                    $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
                    $object->total_pay_without_nds = $objectPayments->where('amount', '<', 0)->sum('amount_without_nds');

                    $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
                    $object->total_receive_without_nds = $objectPayments->sum('amount_without_nds') - $object->total_pay_without_nds;

                    $object->total_balance = $object->total_pay + $object->total_receive;
                    $object->total_balance_without_nds = $object->total_pay_without_nds + $object->total_receive_without_nds;

                    $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount') + $object->transferService()->sum('amount');
                    $object->total_with_general_balance_without_nds = $object->total_pay_without_nds + $object->total_receive_without_nds + ($object->generalCosts()->sum('amount') - round($object->generalCosts()->sum('amount') / 6, 2)) + ($object->transferService()->sum('amount') - round($object->transferService()->sum('amount') / 6, 2));

                    $object->general_balance = $object->total_with_general_balance - $object->total_balance;
                    $object->general_balance_without_nds = $object->total_with_general_balance_without_nds - $object->total_balance_without_nds;

                    $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR);
                    $providerDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER);
                    $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE);

                    $serviceDebtsAmount = $serviceDebts['total']['amount'];
                    $serviceDebtsAmountWithoutNDS = $serviceDebts['total']['amount_without_nds'];

                    $contractorDebtsAmount = $contractorDebts['total']['total_amount'];
                    $contractorDebtsAmountWithoutNDS = $contractorDebts['total']['total_amount_without_nds'];
                    $contractorGuaranteeDebtsAmount = $contractorDebts['total']['guarantee'];

                    $providerDebtsAmount = $providerDebts['total']['amount'];
                    $providerDebtsFixAmount = $providerDebts['total']['amount_fix'];
                    $providerDebtsFloatAmount = $providerDebts['total']['amount_float'];
                    $providerDebtsAmountWithoutNDS = $providerDebts['total']['amount_without_nds'];

                    $ITRSalaryDebt = $object->getITRSalaryDebt();
                    $workSalaryDebt = $object->getWorkSalaryDebt();
                    $workSalaryDebtDetails = $object->getWorkSalaryDebtDetails();
                    $customerDebtInfo = [];
                    $this->contractService->filterContracts(['object_id' => [$object->id]], $customerDebtInfo);
//                $customerDebt = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'] + $customerDebtInfo['avanses_left_amount']['RUB'] + $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');

                    $contractsTotalAmount = $customerDebtInfo['amount']['RUB'];
                    $contractsTotalAmountWithoutNDS = $contractsTotalAmount - round($contractsTotalAmount / 6, 2);

                    $actsTotalAmount = $customerDebtInfo['acts_amount']['RUB'];

                    $dolgZakazchikovZaVipolnenieRaboti = $customerDebtInfo['avanses_acts_left_paid_amount']['RUB'];
                    $dolgZakazchikovZaVipolnenieRabotiWithoutNDS = $dolgZakazchikovZaVipolnenieRaboti - round($dolgZakazchikovZaVipolnenieRaboti / 6, 2);


                    $dolgFactUderjannogoGU = $customerDebtInfo['avanses_acts_deposites_amount']['RUB'] - $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
                    $dolgFactUderjannogoGUWithoutNDS = $dolgFactUderjannogoGU - round($dolgFactUderjannogoGU / 6, 2);

                    // старая версия
//                $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $customerDebtInfo['avanses_notwork_left_amount']['RUB'] - $customerDebtInfo['acts_amount']['RUB'];

                    //новая версия
                    $avansesFixReceived = $customerDebtInfo['avanses_received_amount_fix']['RUB'];
                    $avansesFixReceivedWithoutNDS = $customerDebtInfo['avanses_received_amount_fix']['RUB'] - round($customerDebtInfo['avanses_received_amount_fix']['RUB'] / 6, 2);

                    $avansesFloatReceived = $customerDebtInfo['avanses_received_amount_float']['RUB'];
                    $avansesFloatReceivedWithoutNDS = $customerDebtInfo['avanses_received_amount_float']['RUB'] - round($customerDebtInfo['avanses_received_amount_float']['RUB'] / 6, 2);

                    $avansesReceived = $customerDebtInfo['avanses_received_amount']['RUB'];

                    $actsReceived = $customerDebtInfo['avanses_acts_paid_amount']['RUB'];
                    $actsReceivedWithoutNDS = $customerDebtInfo['avanses_acts_paid_amount']['RUB'] - round($customerDebtInfo['avanses_acts_paid_amount']['RUB'] / 6, 2);

                    $guReceived = $object->guaranteePayments->where('currency', 'RUB')->sum('amount');
                    $guReceivedWithoutNDS = $guReceived - round($guReceived / 6, 2);

                    $ostatokPoDogovoruSZakazchikom = $customerDebtInfo['amount']['RUB'] - $avansesReceived - $actsReceived - $guReceived;
                    $ostatokPoDogovoruSZakazchikomWithoutNDS = $ostatokPoDogovoruSZakazchikom - round($ostatokPoDogovoruSZakazchikom / 6, 2);

                    $ostatokNeotrabotannogoAvansaFix = $customerDebtInfo['avanses_notwork_left_amount_fix']['RUB'];
                    $ostatokNeotrabotannogoAvansaFixWithoutNDS = $ostatokNeotrabotannogoAvansaFix - round($ostatokNeotrabotannogoAvansaFix / 6, 2);

                    $ostatokNeotrabotannogoAvansaFloat = $customerDebtInfo['avanses_notwork_left_amount_float']['RUB'];
                    $ostatokNeotrabotannogoAvansaFloatWithoutNDS = $ostatokNeotrabotannogoAvansaFloat - round($ostatokNeotrabotannogoAvansaFloat / 6, 2);

                    $ostatokNeotrabotannogoAvansa = $customerDebtInfo['avanses_notwork_left_amount']['RUB'];
                    $ostatokNeotrabotannogoAvansaWithoutNDS = $ostatokNeotrabotannogoAvansa - round($ostatokNeotrabotannogoAvansa / 6, 2);

                    $date = now();
                    $EURExchangeRate = $this->currencyExchangeService->getExchangeRate($date->format('Y-m-d'), 'EUR');
                    if ($EURExchangeRate) {
                        $dolgZakazchikovZaVipolnenieRaboti += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
                        $dolgZakazchikovZaVipolnenieRabotiWithoutNDS += ($customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] - round($customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] / 6, 2)) * $EURExchangeRate->rate;

                        $dd = $customerDebtInfo['avanses_acts_deposites_amount']['EUR'] - $object->guaranteePayments->where('currency', 'EUR')->sum('amount');
                        $dolgFactUderjannogoGU += $dd * $EURExchangeRate->rate;
                        $dolgFactUderjannogoGUWithoutNDS += ($dd - round($dd / 6, 2)) * $EURExchangeRate->rate;

                        $ostatokNeotrabotannogoAvansa += ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);
                        $ostatokNeotrabotannogoAvansaWithoutNDS += (($customerDebtInfo['avanses_notwork_left_amount']['EUR'] - round($customerDebtInfo['avanses_notwork_left_amount']['EUR'] / 6, 2)) * $EURExchangeRate->rate);

                        $ostatokNeotrabotannogoAvansaFix += ($customerDebtInfo['avanses_notwork_left_amount_fix']['EUR'] * $EURExchangeRate->rate);
                        $ostatokNeotrabotannogoAvansaFixWithoutNDS += (($customerDebtInfo['avanses_notwork_left_amount_fix']['EUR'] - round($customerDebtInfo['avanses_notwork_left_amount_fix']['EUR'] / 6, 2)) * $EURExchangeRate->rate);

                        $ostatokNeotrabotannogoAvansaFloat += ($customerDebtInfo['avanses_notwork_left_amount_float']['EUR'] * $EURExchangeRate->rate);
                        $ostatokNeotrabotannogoAvansaFloatWithoutNDS += (($customerDebtInfo['avanses_notwork_left_amount_float']['EUR'] - round($customerDebtInfo['avanses_notwork_left_amount_float']['EUR'] / 6, 2)) * $EURExchangeRate->rate);

                        // старая версия
//                    $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_notwork_left_amount']['EUR'] * $EURExchangeRate->rate);
//                    $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['acts_amount']['EUR'] * $EURExchangeRate->rate);

                        //новая версия


                        if ($object->code === '346') {
                            $diff = $customerDebtInfo['amount']['EUR'] - $customerDebtInfo['avanses_received_amount']['EUR'] - $customerDebtInfo['avanses_acts_paid_amount']['EUR'];
                            $ostatokPoDogovoruSZakazchikom += $diff * $EURExchangeRate->rate;
                            $ostatokPoDogovoruSZakazchikom -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount') * $EURExchangeRate->rate;

                            $ostatokPoDogovoruSZakazchikomWithoutNDS += ($diff - round($diff / 6, 2)) * $EURExchangeRate->rate;
                            $ostatokPoDogovoruSZakazchikomWithoutNDS -= ($object->guaranteePayments->where('currency', 'EUR')->sum('amount') - round($object->guaranteePayments->where('currency', 'EUR')->sum('amount') / 6, 2)) * $EURExchangeRate->rate;
                        } else {
                            $ostatokPoDogovoruSZakazchikom += ($customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate);
                            $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_received_amount']['EUR'] * $EURExchangeRate->rate);
                            $ostatokPoDogovoruSZakazchikom -= ($customerDebtInfo['avanses_acts_paid_amount']['EUR'] * $EURExchangeRate->rate);
                            $ostatokPoDogovoruSZakazchikom -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount') * $EURExchangeRate->rate;


                            $ostatokPoDogovoruSZakazchikomWithoutNDS += (($customerDebtInfo['amount']['EUR'] - round($customerDebtInfo['amount']['EUR'] / 6, 2)) * $EURExchangeRate->rate);
                            $ostatokPoDogovoruSZakazchikomWithoutNDS -= (($customerDebtInfo['avanses_received_amount']['EUR'] - round($customerDebtInfo['avanses_received_amount']['EUR'] / 6, 2)) * $EURExchangeRate->rate);
                            $ostatokPoDogovoruSZakazchikomWithoutNDS -= (($customerDebtInfo['avanses_acts_paid_amount']['EUR'] - round($customerDebtInfo['avanses_acts_paid_amount']['EUR'] / 6, 2)) * $EURExchangeRate->rate);
                            $ostatokPoDogovoruSZakazchikomWithoutNDS -= (($object->guaranteePayments->where('currency', 'EUR')->sum('amount') - round($object->guaranteePayments->where('currency', 'EUR')->sum('amount') / 6, 2)) * $EURExchangeRate->rate);
                        }

                        $avansesFixReceived += $customerDebtInfo['avanses_received_amount_fix']['EUR'] * $EURExchangeRate->rate;
                        $avansesFixReceivedWithoutNDS += ($customerDebtInfo['avanses_received_amount_fix']['EUR'] - round($customerDebtInfo['avanses_received_amount_fix']['EUR'] / 6, 2)) * $EURExchangeRate->rate;

                        $avansesFloatReceived += $customerDebtInfo['avanses_received_amount_float']['EUR'] * $EURExchangeRate->rate;
                        $avansesFloatReceivedWithoutNDS += ($customerDebtInfo['avanses_received_amount_float']['EUR'] - round($customerDebtInfo['avanses_received_amount_float']['EUR'] / 6, 2)) * $EURExchangeRate->rate;

                        $avansesReceived += $customerDebtInfo['avanses_received_amount']['EUR'] * $EURExchangeRate->rate;

                        $actsReceived += $customerDebtInfo['avanses_acts_paid_amount']['EUR'] * $EURExchangeRate->rate;
                        $actsReceivedWithoutNDS += ($customerDebtInfo['avanses_acts_paid_amount']['EUR'] - round($customerDebtInfo['avanses_acts_paid_amount']['EUR'] / 6, 2)) * $EURExchangeRate->rate;

                        $guReceived += $object->guaranteePayments->where('currency', 'EUR')->sum('amount') * $EURExchangeRate->rate;
                        $guReceivedWithoutNDS += ($object->guaranteePayments->where('currency', 'EUR')->sum('amount') - round($object->guaranteePayments->where('currency', 'EUR')->sum('amount') / 6, 2)) * $EURExchangeRate->rate;

//                    $customerDebt += $customerDebtInfo['avanses_acts_left_paid_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_left_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt += $customerDebtInfo['avanses_acts_deposites_amount']['EUR'] * $EURExchangeRate->rate;
//                    $customerDebt -= $object->guaranteePayments->where('currency', 'EUR')->sum('amount')  * $EURExchangeRate->rate;

                        $contractsTotalAmount += $customerDebtInfo['amount']['EUR'] * $EURExchangeRate->rate;
                        $contractsTotalAmountWithoutNDS += ($customerDebtInfo['amount']['EUR'] - round($customerDebtInfo['amount']['EUR'] / 6, 2)) * $EURExchangeRate->rate;

                        $actsTotalAmount += $customerDebtInfo['acts_amount']['EUR'] * $EURExchangeRate->rate;
                    }

                    if ($object->code === '361') {
                        $avansesFixReceived = $object->payments()->where('amount', '>', 0)->where('code', '10.2')->sum('amount');
                        $avansesFixReceivedWithoutNDS = $object->payments()->where('amount', '>', 0)->where('code', '10.2')->sum('amount_without_nds');

                        $avansesFloatReceived = $object->payments()->where('amount', '>', 0)->where('code', '10.1')->sum('amount');
                        $avansesFloatReceivedWithoutNDS = $object->payments()->where('amount', '>', 0)->where('code', '10.1')->sum('amount_without_nds');
                    }

                    $receiveFromCustomers = $object->payments()
                        ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                        ->where('amount', '>=', 0)
                        ->where('company_id', 1)
                        ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                        ->sum('amount');
                    $receiveFromCustomersWithoutNDS = $object->payments()
                        ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                        ->where('amount', '>=', 0)
                        ->where('company_id', 1)
                        ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                        ->sum('amount_without_nds');

                    if (count($object->customers->pluck('id')->toArray()) > 0) {
                        $ostatokPoDogovoruSZakazchikom = $contractsTotalAmount - $receiveFromCustomers;
                        $ostatokPoDogovoruSZakazchikomWithoutNDS = $contractsTotalAmountWithoutNDS - $receiveFromCustomersWithoutNDS;
                    }

                    if ($object->code === '288') {
                        // Стефан дал инфо в телеге 23.10.2024
                        $dolgFactUderjannogoGU = 13175716.79;
                        $dolgFactUderjannogoGUWithoutNDS = $dolgFactUderjannogoGU - round($dolgFactUderjannogoGU / 6, 2);
                    }

                    if ($object->code === '000') {
                        $dolgFactUderjannogoGU = $object->guarantees()->sum('amount') - $object->guaranteePayments()->sum('amount');
                        $dolgFactUderjannogoGUWithoutNDS = $dolgFactUderjannogoGU - round($dolgFactUderjannogoGU / 6, 2);
                    }

                    if (!empty($object->closing_date) && $object->status_id === Status::STATUS_BLOCKED) {
                        $ostatokPoDogovoruSZakazchikom = $dolgFactUderjannogoGU;
                        $ostatokPoDogovoruSZakazchikomWithoutNDS = $dolgFactUderjannogoGUWithoutNDS;
                    }

                    if ($object->code === '288') {
                        // Стефан дал инфо в телеге 23.10.2024
                        $ostatokPoDogovoruSZakazchikom = 10443970.23;
                        $ostatokPoDogovoruSZakazchikomWithoutNDS = $ostatokPoDogovoruSZakazchikom - round($ostatokPoDogovoruSZakazchikom / 6, 2);

                        $dolgZakazchikovZaVipolnenieRaboti = 0;
                        $dolgZakazchikovZaVipolnenieRabotiWithoutNDS = 0;
                    }

//                    $taxDebtAmount = TaxPlanItem::where('object_id', $object->id)
//                        ->where('paid', false)
//                        ->whereIn('name', ['НДС', 'Налог на прибыль аванс', 'НДФЛ', 'Транспортный налог', 'Налог на прибыль'])
//                        ->sum('amount');

                    $taxDebtAmount = 0;

                    $totalDebts = $contractorDebtsAmount +
                        $providerDebtsAmount +
                        $serviceDebtsAmount +
                        $ITRSalaryDebt +
                        $workSalaryDebt +
                        $taxDebtAmount;

                    $totalDebtsWithoutNDS = $contractorDebtsAmountWithoutNDS +
                        $providerDebtsAmountWithoutNDS +
                        $serviceDebtsAmountWithoutNDS +
                        $ITRSalaryDebt +
                        $workSalaryDebt +
                        $taxDebtAmount;

                    $totalCustomerDebts = $dolgZakazchikovZaVipolnenieRaboti +
                        $dolgFactUderjannogoGU;
                    $totalCustomerDebtsWithoutNDS = $dolgZakazchikovZaVipolnenieRabotiWithoutNDS +
                        $dolgFactUderjannogoGUWithoutNDS;

                    $objectBalance = $object->total_with_general_balance + $totalCustomerDebts + $totalDebts;
                    $objectBalanceWithoutNDS = $object->total_with_general_balance_without_nds + $totalCustomerDebtsWithoutNDS + $totalDebtsWithoutNDS;

                    $generalBalanceToReceivePercentage = abs($object->total_receive == 0 ? 0 : $object->general_balance / $object->total_receive * 100);

                    // ------ Расчет прогнозируемых затрат

                    if ($object->planPayments->count() === 0) {
                        $this->objectPlanPaymentService->makeDefaultPaymentsForObject($object->id);
                    }

                    $checkPlanPaymentsToCreate = ['planProfitability', 'planProfitability_material', 'planProfitability_rad', 'prognoz_material_fix', 'prognoz_material_float', 'prognoz_consalting_after_work'];

                    foreach ($checkPlanPaymentsToCreate as $value) {
                        if (!$object->planPayments()->where('field', $value)->first()) {
                            PlanPayment::create([
                                'object_id' => $object->id,
                                'field' => $value,
                                'amount' => 0,
                                'type_id' => PlanPayment::TYPE_MANUAL,
                            ]);
                        }
                    }

                    $prognozTotal = 0;
                    $prognozTotalWithoutNDS = 0;
                    $prognozFields = FinanceReport::getPrognozFields();
                    foreach ($prognozFields as $field) {
                        $prognozAmount = -$ostatokPoDogovoruSZakazchikom * FinanceReport::getPercentForField($field);
                        $prognozAmountWithoutNDS = -$ostatokPoDogovoruSZakazchikomWithoutNDS * FinanceReport::getPercentForField($field);

                        if ($field === 'prognoz_consalting') {
                            $prognozAmount = -$ostatokPoDogovoruSZakazchikom * FinanceReport::getPrognozConsaltingPercentForObject($object->code);
                            $prognozAmountWithoutNDS = -$ostatokPoDogovoruSZakazchikomWithoutNDS * FinanceReport::getPrognozConsaltingPercentForObject($object->code);
                        }

                        if ($field === 'prognoz_consalting_after_work' && $object->code !== '373') {
                            $prognozAmount = 0;
                            $prognozAmountWithoutNDS = 0;
                        }

                        if ($field === 'prognoz_podryad') {
                            $prognozAmount = $contractorDebts['total']['balance_contract'];
                            $prognozAmountWithoutNDS = $contractorDebts['total']['balance_contract'];
                        }

                        if ($field === 'prognoz_general') {
//                            if ($avgPercent < -15) {
                            $prognozAmount = -$ostatokPoDogovoruSZakazchikom * (abs($avgPercent) / 100);
                            $prognozAmountWithoutNDS = -$ostatokPoDogovoruSZakazchikomWithoutNDS * (abs($avgPercent) / 100);
//                            }
                        }

                        if (in_array($object->code, $hidePrognozObjectCodes)) {
                            $prognozAmount = 0;
                            $prognozAmountWithoutNDS = 0;
                        }

                        foreach ($object->planPayments as $planPayment) {
                            if ($field === $planPayment->field) {
                                if ($planPayment->isAutoCalculation()) {
                                    $planPayment->update([
                                        'amount' => $prognozAmount
                                    ]);
                                } else {
                                    $prognozAmount = $planPayment->amount;
                                    $prognozAmountWithoutNDS = $planPayment->amount;
                                }

                                break;
                            }
                        }

                        if ($field === 'prognoz_material') {
                            $fixMatPlan = $object->planPayments->where('field', 'prognoz_material_fix')->first();
                            $floatMatPlan = $object->planPayments->where('field', 'prognoz_material_float')->first();

                            if ($fixMatPlan) {
                                if ($fixMatPlan->isAutoCalculation()) {
                                    $amnt = 0;
                                    $cncts = $object->contracts->where('type_id', Contract::TYPE_MAIN);
                                    foreach ($cncts as $cnct) {
                                        $amnt += $cnct->getMaterialAmount();
                                    }

                                    $amnt = $amnt + $object->payments()->where('amount', '<', 0)->where('category', Payment::CATEGORY_MATERIAL)->sum('amount') + $providerDebtsAmount;

                                    if ($object->code === '363' && $floatMatPlan) {
                                        $amnt = $amnt + $floatMatPlan->amount;
                                    }

                                    $fixMatPlan->update([
                                        'amount' => -$amnt
                                    ]);
                                }
                            }

                            $prognozAmount = $object->planPayments->whereIn('field', ['prognoz_material_fix', 'prognoz_material_float'])->sum('amount');
                            $p = $object->planPayments->where('field', 'prognoz_material')->first();
                            if ($p) {
                                $p->update([
                                    'amount' => $prognozAmount
                                ]);
                            }
                        }

                        if ($field === 'prognoz_consalting_after_work') {
                            $total[$year][$object->code]['prognoz_consalting'] += $prognozAmount;
                        } else {
                            $total[$year][$object->code][$field] = $prognozAmount;
                        }

                        if (in_array($field, ['prognoz_material_fix', 'prognoz_material_float'])) {
                            continue;
                        }

                        $prognozTotal += $prognozAmount;
                        $prognozTotalWithoutNDS += $prognozAmountWithoutNDS;
                    }

                    $prognozBalance = $objectBalance + $ostatokPoDogovoruSZakazchikom - $dolgFactUderjannogoGU + $prognozTotal;
                    $prognozBalanceWithoutNDS = $objectBalanceWithoutNDS + $ostatokPoDogovoruSZakazchikomWithoutNDS - $dolgFactUderjannogoGUWithoutNDS + $prognozTotalWithoutNDS;

                    if ($object->code === '000') {
                        $prognozBalance = $objectBalance;
                        $prognozBalanceWithoutNDS = $objectBalanceWithoutNDS;
                    }

                    //----------------------------------------

                    $contracts = $object->contracts;

                    $contractStartDate = '';
                    $contractEndDate = '';
                    $contractLastStartDate = $contracts->where('start_date', '!=', null)->sortBy('start_date', SORT_NATURAL)->first();
                    $contractLastEndDate = $contracts->where('end_date', '!=', null)->sortBy('end_date', SORT_NATURAL)->last();

                    if ($contractLastStartDate) {
                        $contractStartDate = $contractLastStartDate->start_date;
                    }
                    if ($contractLastEndDate) {
                        $contractEndDate = $contractLastEndDate->end_date;
                    }

                    $timePercent = 0;
                    if (!empty($contractStartDate) && !empty($contractEndDate)) {
                        if ($contractEndDate >= now()->format('Y-m-d')) {
                            $current = Carbon::parse($contractStartDate)->diffInDays(now());
                            $fullTime = Carbon::parse($contractStartDate)->diffInDays($contractEndDate);

                            if ($fullTime != 0) {
                                $timePercent = $current / $fullTime * 100;
                            }
                        }
                    }

                    $sContractsTotalAmount += $contractsTotalAmount;
                    $sActsTotalAmount += $actsTotalAmount;
                    $sReceiveFromCustomers += $receiveFromCustomers;
                    $completePercent = $contractsTotalAmount != 0 ? $actsTotalAmount / $contractsTotalAmount * 100 : 0;
                    $moneyPercent = $contractsTotalAmount != 0 ? $receiveFromCustomers / $contractsTotalAmount * 100 : 0;

                    $planReadyPercent = 0;
                    $factReadyPercent = 0;
                    $deviationPlanPercent = 0;

                    $infoName = '';
                    if (isset($objectsFromGromisoft[$object->code])) {
                        $infoName = $objectsFromGromisoft[$object->code];
                    }

                    foreach ($pivotInfoFromGromisoft as $infoFromGromisoft) {
                        if ($infoFromGromisoft['name'] === $infoName) {
                            $planReadyPercent = $infoFromGromisoft['plan_today'];
                            $factReadyPercent = $infoFromGromisoft['fact_today'];
                            $deviationPlanPercent = $infoFromGromisoft['summary_delta'];
                            break;
                        }
                    }

                    $receiveRetroDTG = $object->payments()
                        ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                        ->where('amount', '>=', 0)
                        ->where('company_id', 1)
                        ->where('description', 'LIKE', '%ретроб%')
                        ->sum('amount');
                    $receiveRetroDTGWithoutNDS = $object->payments()
                        ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                        ->where('amount', '>=', 0)
                        ->where('company_id', 1)
                        ->where('description', 'LIKE', '%ретроб%')
                        ->sum('amount_without_nds');

                    $receiveOther = $object->total_receive - $receiveFromCustomers - $receiveRetroDTG;
                    $receiveOtherWithoutNDS = $object->total_receive_without_nds - $receiveFromCustomersWithoutNDS - $receiveRetroDTGWithoutNDS;

                    $payCash = $objectPayments->where('amount', '<', 0)->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)->sum('amount');
                    $payCashWithoutNDS = $objectPayments->where('amount', '<', 0)->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)->sum('amount_without_nds');

                    $payNonCash = $objectPayments->where('amount', '<', 0)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->sum('amount');
                    $payNonCashWithoutNDS = $objectPayments->where('amount', '<', 0)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->sum('amount_without_nds');

                    $payOpste = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_OPSTE)->where('amount', '<', 0)->sum('amount');
                    $payOpsteWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_OPSTE)->where('amount', '<', 0)->sum('amount_without_nds');

                    $payRad = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_RAD)->where('amount', '<', 0)->sum('amount');
                    $payRadWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_RAD)->where('amount', '<', 0)->sum('amount_without_nds');

                    $payMaterial = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->where('amount', '<', 0)->sum('amount');
                    $payMaterialWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->where('amount', '<', 0)->sum('amount_without_nds');

                    $payMaterialFix = 0;
                    $payMaterialFloat = 0;
                    $payMaterialFixWithoutNDS = 0;
                    $payMaterialFloatWithoutNDS = 0;

                    if ($object->code === '360') {
                        $payMaterialFloat = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->where('amount', '<', 0)->where('bank_id', 13)->sum('amount');
                        $payMaterialFloatWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->where('amount', '<', 0)->where('bank_id', 13)->sum('amount_without_nds');

                        $payMaterialFix = $payMaterial - $payMaterialFloat;
                        $payMaterialFixWithoutNDS = $payMaterialWithoutNDS - $payMaterialFloatWithoutNDS;
                    }

                    if ($object->code === '363') {
                        $payMaterialFloat = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->where('amount', '<', 0)->where('bank_id', 15)->sum('amount');
                        $payMaterialFloatWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->where('amount', '<', 0)->where('bank_id', 15)->sum('amount_without_nds');

                        $payMaterialFix = $payMaterial - $payMaterialFloat;
                        $payMaterialFixWithoutNDS = $payMaterialWithoutNDS - $payMaterialFloatWithoutNDS;
                    }

                    $paySalary = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_SALARY)->where('amount', '<', 0)->sum('amount');
                    $paySalaryWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_SALARY)->where('amount', '<', 0)->sum('amount_without_nds');

                    $payTax = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_TAX)->where('amount', '<', 0)->sum('amount');
                    $payTaxWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_TAX)->where('amount', '<', 0)->sum('amount_without_nds');

                    $payCustomers = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_CUSTOMERS)->where('amount', '<', 0)->sum('amount');
                    $payCustomersWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_CUSTOMERS)->where('amount', '<', 0)->sum('amount_without_nds');

                    $payTransfer = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_TRANSFER)->where('amount', '<', 0)->sum('amount');
                    $payTransferWithoutNDS = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_TRANSFER)->where('amount', '<', 0)->sum('amount_without_nds');

                    $payEmpty = Payment::where('object_id', $object->id)->where(function ($q) {
                        $q->whereNull('category');
                        $q->orWhere('category', '');
                    })->where('amount', '<', 0)->sum('amount');
                    $payEmptyWithoutNDS = Payment::where('object_id', $object->id)->where(function ($q) {
                        $q->whereNull('category');
                        $q->orWhere('category', '');
                    })->where('amount', '<', 0)->sum('amount_without_nds');

                    $total[$year][$object->code]['transfer_service'] = $object->transferService()->sum('amount');
                    $total[$year][$object->code]['transfer_service_without_nds'] = $object->transferService()->sum('amount') - round($object->transferService()->sum('amount') / 6, 2);

                    $total[$year][$object->code]['pay'] = $object->total_pay + $total[$year][$object->code]['transfer_service'];
                    $total[$year][$object->code]['pay_without_nds'] = $object->total_pay_without_nds + $total[$year][$object->code]['transfer_service_without_nds'];

                    $total[$year][$object->code]['pay_opste'] = $payOpste;
                    $total[$year][$object->code]['pay_opste_without_nds'] = $payOpsteWithoutNDS;

                    $total[$year][$object->code]['pay_rad'] = $payRad;
                    $total[$year][$object->code]['pay_rad_without_nds'] = $payRadWithoutNDS;

                    $total[$year][$object->code]['pay_material'] = $payMaterial;
                    $total[$year][$object->code]['pay_material_without_nds'] = $payMaterialWithoutNDS;

                    $total[$year][$object->code]['pay_material_fix'] = $payMaterialFix;
                    $total[$year][$object->code]['pay_material_fix_without_nds'] = $payMaterialFixWithoutNDS;

                    $total[$year][$object->code]['pay_material_float'] = $payMaterialFloat;
                    $total[$year][$object->code]['pay_material_float_without_nds'] = $payMaterialFloatWithoutNDS;

                    $total[$year][$object->code]['pay_salary'] = $paySalary;
                    $total[$year][$object->code]['pay_salary_without_nds'] = $paySalaryWithoutNDS;

                    $total[$year][$object->code]['pay_tax'] = $payTax;
                    $total[$year][$object->code]['pay_tax_without_nds'] = $payTaxWithoutNDS;

                    $total[$year][$object->code]['pay_customers'] = $payCustomers;
                    $total[$year][$object->code]['pay_customers_without_nds'] = $payCustomersWithoutNDS;

                    $total[$year][$object->code]['pay_transfer'] = $payTransfer;
                    $total[$year][$object->code]['pay_transfer_without_nds'] = $payTransferWithoutNDS;

                    $total[$year][$object->code]['pay_empty'] = $payEmpty;
                    $total[$year][$object->code]['pay_empty_without_nds'] = $payEmptyWithoutNDS;

                    $total[$year][$object->code]['pay_cash'] = $payCash;
                    $total[$year][$object->code]['pay_cash_without_nds'] = $payCashWithoutNDS;

                    $total[$year][$object->code]['pay_non_cash'] = $payNonCash;
                    $total[$year][$object->code]['pay_non_cash_without_nds'] = $payNonCashWithoutNDS;

                    $total[$year][$object->code]['receive'] = $object->total_receive;
                    $total[$year][$object->code]['receive_without_nds'] = $object->total_receive_without_nds;

                    $total[$year][$object->code]['receive_customer'] = $receiveFromCustomers;
                    $total[$year][$object->code]['receive_customer_without_nds'] = $receiveFromCustomersWithoutNDS;

                    $total[$year][$object->code]['receive_customer_fix_avans'] = $avansesFixReceived;
                    $total[$year][$object->code]['receive_customer_fix_avans_without_nds'] = $avansesFixReceivedWithoutNDS;

                    $total[$year][$object->code]['receive_customer_target_avans'] = $avansesFloatReceived;
                    $total[$year][$object->code]['receive_customer_target_avans_without_nds'] = $avansesFloatReceivedWithoutNDS;

                    $total[$year][$object->code]['receive_customer_acts'] = $actsReceived;
                    $total[$year][$object->code]['receive_customer_acts_without_nds'] = $actsReceivedWithoutNDS;

                    $total[$year][$object->code]['receive_customer_gu'] = $guReceived;
                    $total[$year][$object->code]['receive_customer_gu_without_nds'] = $guReceivedWithoutNDS;

                    $total[$year][$object->code]['receive_retro_dtg'] = $receiveRetroDTG;
                    $total[$year][$object->code]['receive_retro_dtg_without_nds'] = $receiveRetroDTGWithoutNDS;

                    $total[$year][$object->code]['receive_other'] = $receiveOther;
                    $total[$year][$object->code]['receive_other_without_nds'] = $receiveOtherWithoutNDS;

                    $total[$year][$object->code]['balance'] = $object->total_balance + $total[$year][$object->code]['transfer_service'];
                    $total[$year][$object->code]['balance_without_nds'] = $object->total_balance_without_nds + $total[$year][$object->code]['transfer_service'];

                    $total[$year][$object->code]['office_service'] = $object->general_balance - $total[$year][$object->code]['transfer_service'];
                    $total[$year][$object->code]['office_service_without_nds'] = $object->general_balance_without_nds - $total[$year][$object->code]['transfer_service_without_nds'];

                    $total[$year][$object->code]['general_balance'] = $total[$year][$object->code]['office_service'];
                    $total[$year][$object->code]['general_balance_without_nds'] = $total[$year][$object->code]['office_service_without_nds'];

                    $total[$year][$object->code]['general_balance_to_receive_percentage'] = $generalBalanceToReceivePercentage;

                    $total[$year][$object->code]['balance_with_general_balance'] = $object->total_with_general_balance;
                    $total[$year][$object->code]['balance_with_general_balance_without_nds'] = $object->total_with_general_balance_without_nds;

                    $total[$year][$object->code]['contractor_debt'] = $contractorDebtsAmount;
                    $total[$year][$object->code]['contractor_debt_without_nds'] = $contractorDebtsAmountWithoutNDS;

                    $total[$year][$object->code]['contractor_debt_gu'] = $contractorGuaranteeDebtsAmount;
                    $total[$year][$object->code]['contractor_debt_gu_without_nds'] = $contractorGuaranteeDebtsAmount;

                    $total[$year][$object->code]['provider_debt'] = $providerDebtsAmount;
                    $total[$year][$object->code]['provider_debt_fix'] = $providerDebtsFixAmount;
                    $total[$year][$object->code]['provider_debt_float'] = $providerDebtsFloatAmount;
                    $total[$year][$object->code]['provider_debt_without_nds'] = $providerDebtsAmountWithoutNDS;

                    $total[$year][$object->code]['service_debt'] = $serviceDebtsAmount;
                    $total[$year][$object->code]['service_debt_without_nds'] = $serviceDebtsAmountWithoutNDS;

                    $total[$year][$object->code]['tax_debt'] = $taxDebtAmount;
                    $total[$year][$object->code]['tax_debt_without_nds'] = $taxDebtAmount;

//                    $total[$year][$object->code]['itr_salary_debt'] = $ITRSalaryDebt;

                    $total[$year][$object->code]['workers_salary_debt'] = $workSalaryDebt;
                    $total[$year][$object->code]['workers_salary_debt_without_nds'] = $workSalaryDebt;

                    $total[$year][$object->code]['dolgZakazchikovZaVipolnenieRaboti'] = $dolgZakazchikovZaVipolnenieRaboti;
                    $total[$year][$object->code]['dolgZakazchikovZaVipolnenieRaboti_without_nds'] = $dolgZakazchikovZaVipolnenieRabotiWithoutNDS;

                    $total[$year][$object->code]['dolgFactUderjannogoGU'] = $dolgFactUderjannogoGU;
                    $total[$year][$object->code]['dolgFactUderjannogoGU_without_nds'] = $dolgFactUderjannogoGUWithoutNDS;

                    $total[$year][$object->code]['objectBalance'] = $objectBalance;
                    $total[$year][$object->code]['objectBalance_without_nds'] = $objectBalanceWithoutNDS;

                    $total[$year][$object->code]['contractsTotalAmount'] = $contractsTotalAmount;
                    $total[$year][$object->code]['contractsTotalAmount_without_nds'] = $contractsTotalAmountWithoutNDS;

                    $total[$year][$object->code]['ostatokNeotrabotannogoAvansa'] = $ostatokNeotrabotannogoAvansa;
                    $total[$year][$object->code]['ostatokNeotrabotannogoAvansa_without_nds'] = $ostatokNeotrabotannogoAvansaWithoutNDS;

                    $total[$year][$object->code]['ostatokNeotrabotannogoAvansaFix'] = $ostatokNeotrabotannogoAvansaFix;
                    $total[$year][$object->code]['ostatokNeotrabotannogoAvansaFix_without_nds'] = $ostatokNeotrabotannogoAvansaFixWithoutNDS;

                    $total[$year][$object->code]['ostatokNeotrabotannogoAvansaFloat'] = $ostatokNeotrabotannogoAvansaFloat;
                    $total[$year][$object->code]['ostatokNeotrabotannogoAvansaFloat_without_nds'] = $ostatokNeotrabotannogoAvansaFloatWithoutNDS;

                    $total[$year][$object->code]['ostatokPoDogovoruSZakazchikom'] = $ostatokPoDogovoruSZakazchikom;
                    $total[$year][$object->code]['ostatokPoDogovoruSZakazchikom_without_nds'] = $ostatokPoDogovoruSZakazchikomWithoutNDS;

                    $total[$year][$object->code]['prognoz_total'] = $prognozTotal;

                    $total[$year][$object->code]['prognozBalance'] = $prognozBalance;
                    $total[$year][$object->code]['prognozBalance_without_nds'] = $prognozBalanceWithoutNDS;

                    $total[$year][$object->code]['time_percent'] = $timePercent;
                    $total[$year][$object->code]['complete_percent'] = $completePercent;
                    $total[$year][$object->code]['money_percent'] = $moneyPercent;
                    $total[$year][$object->code]['plan_ready_percent'] = $planReadyPercent;
                    $total[$year][$object->code]['fact_ready_percent'] = $factReadyPercent;
                    $total[$year][$object->code]['deviation_plan_percent'] = $deviationPlanPercent;

                    $total[$year][$object->code]['planProfitability_material'] = $object->planPayments()->where('field', 'planProfitability_material')->first()->amount;
                    $total[$year][$object->code]['planProfitability_rad'] = $object->planPayments()->where('field', 'planProfitability_rad')->first()->amount;
                    $total[$year][$object->code]['planProfitability'] = $total[$year][$object->code]['planProfitability_material'] + $total[$year][$object->code]['planProfitability_rad'];

                    $total[$year][$object->code]['total_debts'] = $totalDebts;
                    $total[$year][$object->code]['total_debts_without_nds'] = $totalDebts;

                    $total[$year][$object->code]['customer_debts'] = $totalCustomerDebts;
                    $total[$year][$object->code]['customer_debts_without_nds'] = $totalCustomerDebts;

                    $total[$year][$object->code]['general_balance_salary'] = 0;
                    $total[$year][$object->code]['general_balance_tax'] = 0;
                    $total[$year][$object->code]['general_balance_material'] = 0;
                    $total[$year][$object->code]['general_balance_service'] = 0;

                    foreach ($total[$year][$object->code] as $key => $value) {
                        $summary[$year][$key] += (float)$value;
                    }
                }

                $summary[$year]['general_balance_to_receive_percentage'] = $sReceiveFromCustomers == 0 ? 0 : $summary[$year]['general_balance'] / $sReceiveFromCustomers * 100;
                $summary[$year]['time_percent'] = 0;
                $summary[$year]['complete_percent'] = $sContractsTotalAmount != 0 ? $sActsTotalAmount / $sContractsTotalAmount * 100 : 0;
                $summary[$year]['money_percent'] = $sContractsTotalAmount != 0 ? $sReceiveFromCustomers / $sContractsTotalAmount * 100 : 0;

                $summary[$year]['plan_ready_percent'] = 0;
                $summary[$year]['fact_ready_percent'] = 0;
                $summary[$year]['deviation_plan_percent'] = 0;

                $summary[$year]['tax_debt'] = -TaxPlanItem::where('object_id', 0)
                    ->where('paid', false)
                    ->where('due_date', '<', Carbon::now())
                    ->whereIn('name', ['НДС', 'Налог на прибыль аванс', 'НДФЛ', 'Транспортный налог', 'Налог на прибыль', 'Страховые взносы'])
                    ->sum('amount');

                $summary[$year]['objectBalance'] += $summary[$year]['tax_debt'];
                $summary[$year]['prognozBalance'] += $summary[$year]['tax_debt'];

                $totalPercentsForGeneralCosts = $this->generalReportService->getSplitPercentsByCategory(['2025', '2024', '2023', '2022', '2021']);

                $summary[$year]['general_balance_salary'] = $totalPercentsForGeneralCosts[Payment::CATEGORY_SALARY] * $summary[$year]['general_balance'];
                $summary[$year]['general_balance_tax'] = $totalPercentsForGeneralCosts[Payment::CATEGORY_TAX] * $summary[$year]['general_balance'];
                $summary[$year]['general_balance_material'] = $totalPercentsForGeneralCosts[Payment::CATEGORY_MATERIAL] * $summary[$year]['general_balance'];
                $summary[$year]['general_balance_service'] = $totalPercentsForGeneralCosts[Payment::CATEGORY_OPSTE] * $summary[$year]['general_balance'];
            }
        } catch (\Exception $e) {
            dd($e);
            $this->sendErrorMessage('Ошибка в расчете: ' . $e->getMessage());
            $this->endProcess();
            return 0;
        }

        $objects = [];
        $infos = [];
        $objects_new = json_encode(compact('total', 'infos', 'summary', 'objects', 'years'));

        $date = Carbon::now()->format('Y-m-d');
        $financeReportHistory = FinanceReportHistory::where('date', $date)->first();
        // В этом месте можно будет регулировать, сохранять ли каждый раз новую копию, или перезаписывать существующую на один и тот же день
        if (!$financeReportHistory) {
            FinanceReportHistory::create([
                'date' => $date,
                'balances' => json_encode([]),
                'credits' => json_encode([]),
                'loans' => json_encode([]),
                'deposits' => json_encode([]),
                'objects' => json_encode([]),
                'objects_new' => json_encode([]),
            ]);
        }

        $financeReportHistory = FinanceReportHistory::where('date', $date)->first();
        $financeReportHistory->update(compact('balances', 'credits', 'loans', 'deposits', 'objects_new'));

        $this->sendInfoMessage('Создание прошло успешно');
        $this->sendInfoMessage('Выгрущка снимка финансового отчета в Excel');

        $fileName = 'Финансовый_отчет_' . now()->format('d_m_Y') . '.xlsx';

        $balancesInfo = json_decode($financeReportHistory->balances);
        $creditsInfo = json_decode($financeReportHistory->credits);
        $loansInfo = json_decode($financeReportHistory->loans);
        $depositsInfo = json_decode($financeReportHistory->deposits);
        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $loansGroupInfo = FinanceReport::getLoansGroupInfo();

        Excel::store(
            new Export(
                [
                    'balancesInfo' => $balancesInfo,
                    'creditsInfo' => $creditsInfo,
                    'loansInfo' => $loansInfo,
                    'depositsInfo' => $depositsInfo,
                    'objectsInfo' => $objectsInfo,
                    'loansGroupInfo' => $loansGroupInfo,
                ],
                [
                    'show_closed_objects' => false,
                    'act_service' => $this->actService,
                ]
            ),
            '/exports/finance/' . $fileName,
            'public',
            \Maatwebsite\Excel\Excel::XLSX
        );

        $this->sendInfoMessage('Экспорт прошел успешно');

        $this->endProcess();

        return 0;
    }
}
