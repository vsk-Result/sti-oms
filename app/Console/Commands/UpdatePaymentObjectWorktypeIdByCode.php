<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Payment;

class UpdatePaymentObjectWorktypeIdByCode extends HandledCommand
{
    protected $signature = 'oms:update-payment-object-worktypeid-by-codes';

    protected $description = 'Исправляет вид работы у оплат объекта по статье затрат';

    protected string $period = 'Вручную';

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

        $this->sendInfoMessage('Старт исправления статей затрат оплат');

        Payment::whereBetween('date', ['2022-01-01', '2025-12-31'])->chunk(1000, function($payments) {
            foreach ($payments as $payment) {
                if (! empty($payment->code) && !is_null($payment->code)) {
                    $payment->update([
                        'object_worktype_id' => $this->getObjectWorktypeByCode($payment->code)
                    ]);
                }
            }
        });

        $this->sendInfoMessage('Исправление вида работ завершено');

        $this->endProcess();

        return 0;
    }

    public function getObjectWorktypeByCode($code)
    {
        if (str_contains($code, '.')) {
            $realCode = (int) substr($code, 0, strpos($code, '.'));
        } else {
            $realCode = (int) $code;
        }

        if ($realCode < 1 || $realCode > 10) {
            return null;
        }

        return $realCode;
    }
}
