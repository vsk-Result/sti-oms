<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\CashCheck\CashCheckService;

use Illuminate\Support\Facades\Mail;

class NotifyToEmailAboutCRMCashCheck extends HandledCommand
{
    protected $signature = 'oms:notify-to-email-about-crm-cash-check';

    protected $description = 'Отправляет на почту пользователю CRM о том, что он может закрыть запрашиваемый период';

    protected string $period = 'Каждые 10 минут';

    public function __construct(private CashCheckService $cashCheckService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $checks = $this->cashCheckService->getCheckedToEmailChecks();

        $this->sendInfoMessage('Найдено ' . $checks->count() . ' заявок к отправке');

        $successSended = 0;
        foreach ($checks as $check) {
            try {
                Mail::send('emails.crm-cash-check.checked_notify', compact('check'), function ($m) use ($check) {
                    $m->from('support@st-ing.com', 'OMS Support');
                    $m->subject('OMS. Закрытый период ' . $check->getFormattedPeriod() . ' можно закрыть');

                    $m->to($check->crmUser->email);
                    $m->to('result007@yandex.ru');
                });

                $this->cashCheckService->checkSended($check);
                $successSended++;
            } catch(Exception $e) {
                $this->sendErrorMessage('Не удалось отправить уведомление на email: "' . $e->getMessage());
                $this->cashCheckService->checkSendedWithError($check);
            }
        }

        $this->sendInfoMessage('Успешно отправлено ' . $successSended . ' писем');

        $this->endProcess();

        return 0;
    }
}
