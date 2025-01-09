<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\CRM\CostItem;
use App\Models\KostCode;
use App\Models\Payment;

class ClearCRMPaymentCodes extends HandledCommand
{
    protected $signature = 'oms:clear-crm-payment-codes';

    protected $description = 'Очищает статьи затрат оплат в кассах CRM';

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

        $this->sendInfoMessage('Найдено ' . CostItem::whereNotIn('new_cost_code', array_merge(['7.3', '7.11', '7.8', '7.9'], KostCode::getCodesForPayment()))->count() . ' оплат');

        CostItem::whereNotIn('new_cost_code', array_merge(['7.3', '7.11', '7.8', '7.9'], KostCode::getCodesForPayment()))->update([
            'new_cost_code' => '0'
        ]);

        $this->sendInfoMessage('Очищение лишних статей затрат прошло успешно');

        $this->endProcess();

        return 0;
    }
}
