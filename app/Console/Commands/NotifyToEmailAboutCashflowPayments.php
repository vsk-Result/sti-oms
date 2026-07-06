<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\Object\BObject;
use App\Models\Object\ResponsiblePersonPosition;
use App\Services\ObjectBalanceExportService;
use App\Services\ReceivePlanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class NotifyToEmailAboutCashflowPayments extends HandledCommand
{
    protected $signature = 'oms:notify-to-email-about-cashflow-payments';

    protected $description = 'Отправляет на почту сотрудникам информацию о платежах cashflow за след неделю';

    protected string $period = 'По понедельникам и пятницам 09:00';

    public function __construct(private ReceivePlanService $receivePlanService)
    {
        parent::__construct();
    }

    public function handle()
    {
//        if (! Carbon::now()->isMonday()) {
//            return 0;
//        }
//
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        $periods = $this->receivePlanService->getPeriods();

        $currentPayments = [];
        $planPayments = PlanPayment::where('need_notification', true)->get();
        foreach ($planPayments as $planPayment) {
            $amount = $planPayment->entries->whereBetween('date', [$periods[0]['start'], $periods[0]['end']])->sum('amount');
            if ($amount != 0) {
                $currentPayments[$planPayment->name] = $amount;
            }
        }

        $nextPayments = [];
        foreach ($planPayments as $planPayment) {
            $amount = $planPayment->entries->whereBetween('date', [$periods[1]['start'], $periods[1]['end']])->sum('amount');
            if ($amount != 0) {
                $nextPayments[$planPayment->name] = $amount;
            }
        }

        if (now()->isFriday()) {
            $currentPayments = [];
        }

        $currentPeriod = $periods[0]['format'];
        $nextPeriod = $periods[1]['format'];

        try {
            Mail::send('emails.cash-flow.plan_payments', compact('currentPayments', 'nextPayments', 'currentPeriod', 'nextPeriod'), function ($m) {
                $m->from('support@st-ing.com', 'OMS Support');
                $m->subject('OMS. Плановые оплаты CASH FLOW');

                $m->to('inna.kamennaya@dttermo.ru');
                $m->to('viktoriya.vujisich@st-ing.com');
                $m->cc('result007@yandex.ru');
                $m->cc('oksana.dashenko@st-ing.com');
            });
        } catch(Exception $e) {
            $this->sendErrorMessage('Не удалось отправить уведомление на email: "' . $e->getMessage());
        }

//        $this->endProcess();

        return 0;
    }
}
