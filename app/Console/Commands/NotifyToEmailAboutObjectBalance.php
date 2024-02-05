<?php

namespace App\Console\Commands;

use App\Models\Object\BObject;
use App\Services\CRONProcessService;
use App\Services\ObjectBalanceExportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyToEmailAboutObjectBalance extends Command
{
    protected $signature = 'oms:notify-to-email-about-object-balance';

    protected $description = 'Отправляет на почту сотрудникам информацию о балансе объектов';

    private ObjectBalanceExportService $balanceExportService;

    public function __construct(CRONProcessService $CRONProcessService, ObjectBalanceExportService $balanceExportService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 15:00'
        );
        $this->balanceExportService = $balanceExportService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Отправка на почту сотрудникам информацию о балансе объектов');

        $alwaysInCopy = ['oksana.dashenko@st-ing.com', 'enes@st-ing.com', 'result007@yandex.ru'];
        $notificationConfig = [
            '346' => ['kseniya.boltisheva@st-ing.com'], // Октафарма
            '353' => ['natalya.vorotnikova@st-ing.com'], // Сухаревская (ЖК "Лайон Гейт")
            '358' => ['galina.strutinskaya@st-ing.com'], // Завидово
            '360' => ['stefan.stamenkovich@st-ing.com', 'elena.pushkina@st-ing.com', 'mariya.serebryakova@st-ing.com'], // Тинькоф
            '361' => ['kseniya.boltisheva@st-ing.com'], // Кемерово
            '363' => ['natalya.vorotnikova@st-ing.com', 'ekaterina.dulesova@st-ing.ru'], // Камчатка
            '364' => ['galina.strutinskaya@st-ing.com'], // Гольф-клуб Завидово
            '365' => ['stefan.stamenkovich@st-ing.com', 'elena.pushkina@st-ing.com', 'mariya.serebryakova@st-ing.com'], // Аэрофлот
            '366' => ['kseniya.boltisheva@st-ing.com'], // Валента
            '367' => ['stefan.stamenkovich@st-ing.com', 'elena.pushkina@st-ing.com', 'mariya.serebryakova@st-ing.com'], // Офис Веспер
        ];

        $now = Carbon::now()->format('d.m.Y');

        foreach ($notificationConfig as $objectCode => $receivers) {
            $object = BObject::where('code', $objectCode)->first();
            $filepath = $this->balanceExportService->store($object);

            try {
                Mail::send('emails.objects.balance', compact('now', 'object'), function ($m) use ($now, $object, $receivers, $alwaysInCopy, $filepath) {
                    $m->from('support@st-ing.com', 'OMS Support');
                    $m->subject('OMS. ' . $now . ' Отчет о балансе объекта ' . $object->getName());
                    $m->attach($filepath);

                    foreach ($receivers as $receiver) {
                        $m->to($receiver);
                    }

                    foreach ($alwaysInCopy as $receiver) {
                        $m->cc($receiver);
                    }
                });
            } catch(Exception $e){
                Log::channel('custom_imports_log')->debug('[ERROR] Не удалось отправить уведомление на email: "' . $e->getMessage());
            }
        }

        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
