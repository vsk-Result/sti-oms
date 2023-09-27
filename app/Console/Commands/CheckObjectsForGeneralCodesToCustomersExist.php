<?php

namespace App\Console\Commands;

use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckObjectsForGeneralCodesToCustomersExist extends Command
{
    protected $signature = 'oms:check-objects-for-general-codes-to-customers-exist';

    protected $description = 'Проверяет объекты для расчета общих затрат на наличие заказчиков';

    public function __construct(CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 07:00'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Проверка объектов для расчета общих затрат на наличие заказчиков');

        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objects = BObject::whereIn('code', $codes)->with('customers')->orderBy('code')->get();

        $invalidObjects = [];
        foreach ($objects as $object) {
            if ($object->customers->count() === 0) {
                $invalidObjects[] = $object->getName();
                Log::channel('custom_imports_log')->debug('[ERROR] У объекта ' . $object->getName() . ' не указаны заказчики');
            }
        }

        if (count($invalidObjects) > 0) {
            try {
                Mail::send('emails.objects.general_costs_customers', compact('invalidObjects'), function ($m) {
                    $m->from('support@st-ing.com', 'OMS Support');
                    $m->to('oksana.dashenko@st-ing.com');
                    $m->cc('result007@yandex.ru')
                        ->subject('OMS. Проблемные объекты для расчета общих затрат');
                });
            } catch(\Exception $e){
                Log::channel('custom_imports_log')->debug('[ERROR] Не удалось отправить уведомление на email: "' . $e->getMessage());
            }
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] ' . count($invalidObjects) . ' проблемных объектов выявлено');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
