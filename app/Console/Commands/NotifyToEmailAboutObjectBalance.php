<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use App\Models\Object\ResponsiblePersonPosition;
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
//        if (Carbon::now()->isSaturday() || Carbon::now()->isSunday()) {
//            return 0;
//        }
//
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        $alwaysInCopy = ['oksana.dashenko@st-ing.com', 'enes@st-ing.com'];

        $notificationConfig = [];
        foreach(BObject::active()->orderByDesc('code')->get() as $object) {
            $managers = $object->responsiblePersons()->whereIn('position_id', ResponsiblePersonPosition::getFinanceManagerPositions())->orderBy('fullname')->get();

            foreach ($managers as $manager) {
                if (! empty($manager->email)) {
                    $notificationConfig[$object->code][] = $manager->email;
                }
            }

            if (isset($notificationConfig[$object->code])) {
                $notificationConfig[$object->code] = array_unique($notificationConfig[$object->code]);
            }
        }

        dd($notificationConfig);

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
                        $m->to('result007@yandex.ru');
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
