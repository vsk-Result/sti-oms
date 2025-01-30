<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use App\Models\Object\ResponsiblePersonPosition;
use App\Services\ObjectBalanceExportService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;

class NotifyToBossesEmailAboutObjectBalance extends HandledCommand
{
    protected $signature = 'oms:notify-to-bosses-email-about-object-balance';

    protected $description = 'Отправляет на почту руководителям информацию о балансе объектов';

    protected string $period = 'По понедельникам в 16:00';

    public function __construct(private ObjectBalanceExportService $balanceExportService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

//        $notificationConfig = [
//            '353' => ['oleg.kalin@st-ing.com'], // Сухаревская (ЖК "Лайон Гейт")
//            '358' => ['maxim.generalov@st-ing.com'], // Завидово
//            '360' => ['aleksandar.lazarevic@st-ing.com'], // Тинькоф
//            '361' => ['petar.evtich@st-ing.com', 'pavel.kroviakov@st-ing.com'], // Кемерово
//            '363' => ['andrei.sokalskii@st-ing.com'], // Камчатка
//            '364' => ['maxim.generalov@st-ing.com'], // Гольф-клуб Завидово
//            '365' => ['oleg.kalin@st-ing.com'], // Аэрофлот
//            '366' => ['vladimir.vilotievich@st-ing.com'], // Валента
//            '367' => ['oleg.kalin@st-ing.com'], // Офис Веспер
//            '369' => ['oleg.kalin@st-ing.com'], // Mono Space
//            '373' => ['sergei.borisov@st-ing.com'], // Детский центр Магнитогорск
//        ];

        $notificationConfig = [];
        foreach(BObject::active()->orderByDesc('code')->get() as $object) {
            $bosses = $object->responsiblePersons()->whereIn('position_id', ResponsiblePersonPosition::getMainPositions())->orderBy('fullname')->get();

            foreach ($bosses as $boss) {
                if (! empty($boss->email)) {
                    $notificationConfig[$object->code][] = $boss->email;
                }
            }

            if (isset($notificationConfig[$object->code])) {
                $notificationConfig[$object->code] = array_unique($notificationConfig[$object->code]);
            }
        }

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
            } catch(Exception $e){
                $this->sendErrorMessage('Не удалось отправить уведомление на email: "' . $e->getMessage());
            }
        }

        $this->endProcess();

        return 0;
    }
}
