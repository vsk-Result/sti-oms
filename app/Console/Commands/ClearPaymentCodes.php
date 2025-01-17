<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\KostCode;
use App\Models\Payment;

class ClearPaymentCodes extends HandledCommand
{
    protected $signature = 'oms:clear-payment-codes';

    protected $description = 'Очищает статьи затрат оплат';

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

        $this->sendInfoMessage('Очищение лишних статей затрат');

        $this->sendInfoMessage('Найдено ' . Payment::whereNotIn('code', array_keys(KostCode::getCodesWithId()))->count() . ' оплат');

        Payment::whereNotIn('code', array_keys(KostCode::getCodesWithId()))->update([
            'code' => '0'
        ]);

        $this->sendInfoMessage('Очищение лишних статей затрат прошло успешно');

        $this->endProcess();

        return 0;
    }
}
