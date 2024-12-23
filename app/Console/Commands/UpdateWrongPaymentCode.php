<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Payment;

class UpdateWrongPaymentCode extends HandledCommand
{
    protected $signature = 'oms:update-wrong-payment-code';

    protected $description = 'Исправляет некорректные статьи затрат в оплатах';

    protected string $period = 'Ежедневно в 20:00';

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

        $this->sendInfoMessage('Старт исправления оплат с запятой');

        $count = 0;
        Payment::where('code', 'LIKE', '%,%')->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $payment->update([
                    'code' => str_replace(',', '.', $payment->code)
                ]);
                $count++;
            }
        });

        $this->sendInfoMessage('Исправлено ' . $count . ' оплат с запятой');

        $count = 0;
        Payment::where('code', 'LIKE', '%..%')->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $payment->update([
                    'code' => str_replace('..', '.', $payment->code)
                ]);
                $count++;
            }
        });

        $this->sendInfoMessage('Исправлено ' . $count . ' оплат с двумя точками');

        $count = 0;
        Payment::where('code', 'LIKE', '% %')->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $payment->update([
                    'code' => str_replace(' ', '', $payment->code)
                ]);
                $count++;
            }
        });

        $this->sendInfoMessage('Исправлено ' . $count . ' оплат с пробелами');

        $count = 0;
        Payment::where('code', 'LIKE', '%.%')->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                if (str_ends_with($payment->code, '.')) {
                    $payment->update([
                        'code' => substr($payment->code, 0, strlen($payment->code) - 1)
                    ]);
                    $count++;
                }
            }
        });

        $this->sendInfoMessage('Исправлено ' . $count . ' оплат с последней точкой');

        $this->sendInfoMessage('Исправление статей затрат в оплатах завершено');

        $this->endProcess();

        return 0;
    }
}
