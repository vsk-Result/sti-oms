<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use App\Services\ObjectBalanceExportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class NotifyToEmailAboutObjectBalance extends HandledCommand
{
    protected $signature = 'oms:notify-to-email-about-object-balance';

    protected $description = 'Отправляет на почту сотрудникам информацию о балансе объектов';

    protected string $period = 'Ежедневно в 15:00';

    public function __construct(private ObjectBalanceExportService $balanceExportService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if (Carbon::now()->isSaturday() || Carbon::now()->isSunday()) {
            return 0;
        }

        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $alwaysInCopy = ['oksana.dashenko@st-ing.com', 'enes@st-ing.com', 'result007@yandex.ru'];
        $notificationConfig = [
            '346' => ['kseniya.boltisheva@st-ing.com'], // Октафарма
            '353' => ['natalya.vorotnikova@st-ing.com'], // Сухаревская (ЖК "Лайон Гейт")
            '358' => ['galina.strutinskaya@st-ing.com'], // Завидово
            '360' => ['stefan.stamenkovich@st-ing.com', 'elena.pushkina@st-ing.com', 'mariya.serebryakova@st-ing.com'], // Тинькоф
            '361' => ['kseniya.boltisheva@st-ing.com'], // Кемерово
            '363' => ['natalya.vorotnikova@st-ing.com', 'ekaterina.dulesova@st-ing.com'], // Камчатка
            '364' => ['galina.strutinskaya@st-ing.com'], // Гольф-клуб Завидово
            '365' => ['stefan.stamenkovich@st-ing.com', 'elena.pushkina@st-ing.com', 'mariya.serebryakova@st-ing.com'], // Аэрофлот
            '366' => ['kseniya.boltisheva@st-ing.com'], // Валента
            '367' => ['stefan.stamenkovich@st-ing.com', 'elena.pushkina@st-ing.com', 'mariya.serebryakova@st-ing.com'], // Офис Веспер
            '369' => ['stefan.stamenkovich@st-ing.com', 'elena.pushkina@st-ing.com'], // Mono Space
            '373' => ['natalya.vorotnikova@st-ing.com'], // Детский центр Магнитогорск
        ];

        $now = Carbon::now()->format('d.m.Y');

        foreach ($notificationConfig as $objectCode => $receivers) {
            $object = BObject::where('code', $objectCode)->first();
            $filepath = $this->balanceExportService->store($object);

            try {
                Mail::send('emails.objects.balance', compact('now', 'object'), function ($m) use ($now, $object, $receivers, $filepath) {
                    $m->from('support@st-ing.com', 'OMS Support');
                    $m->subject('OMS. ' . $now . ' Отчет о балансе объекта ' . $object->getName());
                    $m->attach($filepath);

                    foreach ($receivers as $receiver) {
                        $m->to($receiver);
                    }
                });
            } catch(Exception $e) {
                $this->sendErrorMessage('Не удалось отправить уведомление на email: "' . $e->getMessage());
            }
        }

        try {
            Mail::send('emails.objects.general_balance', compact('now'), function ($m) use ($now, $alwaysInCopy, $notificationConfig) {
                $m->from('support@st-ing.com', 'OMS Support');
                $m->subject('OMS. ' . $now . ' Отчет о балансе объектов');

                foreach ($notificationConfig as $objectCode => $receivers) {
                    $object = BObject::where('code', $objectCode)->first();
                    $filepath = $this->balanceExportService->store($object);
                    $m->attach($filepath);
                }

                foreach ($alwaysInCopy as $receiver) {
                    $m->to($receiver);
                }
            });
        } catch(Exception $e){
            $this->sendErrorMessage('Не удалось отправить общее уведомление на email: "' . $e->getMessage());
        }

        $this->endProcess();

        return 0;
    }
}
