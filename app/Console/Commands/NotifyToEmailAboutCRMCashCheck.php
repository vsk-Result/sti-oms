<?php

namespace App\Console\Commands;

use App\Models\CRM\Cost;
use App\Services\CashCheck\CashCheckService;
use App\Services\CRONProcessService;
use App\Services\ObjectBalanceExportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyToEmailAboutCRMCashCheck extends Command
{
    protected $signature = 'oms:notify-to-email-about-crm-cash-check';

    protected $description = 'Отправляет на почту пользователю CRM о том, что он может закрыть запрашиваемый период';

    public function __construct(private CRONProcessService $CRONProcessService, private CashCheckService $cashCheckService)
    {
        parent::__construct();
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Каждые 10 минут'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Отправка на почту пользователю CRM письмо, что он может закрыть запрашиваемый период');

        $checks = $this->cashCheckService->getCheckedToEmailChecks();

        Log::channel('custom_imports_log')->debug('[INFO] Найдено ' . $checks->count() . ' заявок к отправке');

        $successSended = 0;
        foreach ($checks as $check) {
            try {
                Mail::send('emails.crm-cash-check.checked_notify', compact('check'), function ($m) use ($check) {
                    $m->from('support@st-ing.com', 'OMS Support');
                    $m->subject('OMS. Закрытый период ' . $check->getFormattedPeriod() . ' можно закрыть');

//                    $m->to($check->crmUser->email);
                    $m->to('result007@yandex.ru');
                });

                $this->cashCheckService->checkSended($check);
                $successSended++;
            } catch(Exception $e){
                Log::channel('custom_imports_log')->debug('[ERROR] Не удалось отправить уведомление на email: "' . $e->getMessage());
                $this->cashCheckService->checkSendedWithError($check);
            }
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Успешно отправлено ' . $successSended . ' писем');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
