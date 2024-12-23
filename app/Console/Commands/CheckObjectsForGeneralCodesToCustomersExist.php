<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use Illuminate\Support\Facades\Mail;

class CheckObjectsForGeneralCodesToCustomersExist extends HandledCommand
{
    protected $signature = 'oms:check-objects-for-general-codes-to-customers-exist';

    protected $description = 'Проверяет объекты для расчета общих затрат на наличие заказчиков';

    protected string $period = 'Ежедневно в 07:00';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objects = BObject::whereIn('code', $codes)->with('customers')->orderBy('code')->get();

        $invalidObjects = [];
        foreach ($objects as $object) {
            if ($object->customers->count() === 0) {
                $invalidObjects[] = $object->getName();
                $this->sendErrorMessage('У объекта ' . $object->getName() . ' не указаны заказчики');
            }
        }

        if (count($invalidObjects) > 0) {
            try {
                Mail::send('emails.objects.general_costs_customers', compact('invalidObjects'), function ($m) {
                    $m->from('support@st-ing.com', 'OMS Support');
                    $m->to('result007@yandex.ru')
                        ->subject('OMS. Проблемные объекты для расчета общих затрат');
                });
            } catch(\Exception $e) {
                $this->sendErrorMessage('Не удалось отправить уведомление на email: "' . $e->getMessage());
            }
        }

        $this->sendInfoMessage(count($invalidObjects) . ' проблемных объектов выявлено');

        $this->endProcess();

        return 0;
    }
}
