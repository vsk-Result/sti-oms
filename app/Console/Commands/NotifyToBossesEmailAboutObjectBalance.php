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

class NotifyToBossesEmailAboutObjectBalance extends Command
{
    protected $signature = 'oms:notify-to-bosses-email-about-object-balance';

    protected $description = 'Отправляет на почту руководителям информацию о балансе объектов';

    private ObjectBalanceExportService $balanceExportService;

    public function __construct(CRONProcessService $CRONProcessService, ObjectBalanceExportService $balanceExportService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'По понедельникам в 16:00'
        );
        $this->balanceExportService = $balanceExportService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Отправка на почту руководителям информацию о балансе объектов');

        $notificationConfig = [
            '353' => ['oleg.kalin@st-ing.com'], // Сухаревская (ЖК "Лайон Гейт")
            '358' => ['maxim.generalov@st-ing.com'], // Завидово
            '360' => ['aleksandar.lazarevic@st-ing.com'], // Тинькоф
            '361' => ['petar.evtich@st-ing.com', 'pavel.kroviakov@st-ing.com'], // Кемерово
            '363' => ['andrei.sokalskii@st-ing.com'], // Камчатка
            '364' => ['maxim.generalov@st-ing.com'], // Гольф-клуб Завидово
            '365' => ['oleg.kalin@st-ing.com'], // Аэрофлот
            '366' => ['vladimir.vilotievich@st-ing.com'], // Валента
            '367' => ['oleg.kalin@st-ing.com'], // Офис Веспер
            '369' => ['oleg.kalin@st-ing.com'], // Mono Space
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
            } catch(Exception $e){
                Log::channel('custom_imports_log')->debug('[ERROR] Не удалось отправить уведомление на email: "' . $e->getMessage());
            }
        }

        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
