<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Payment;

class UpdateSplitPaymentCategoryFromCode extends HandledCommand
{
    protected $signature = 'oms:update-split-payment-category-from-code';

    protected $description = 'Исправляет категории в оплатах, разбитых из CRM на основе статей зартат';

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

        $count = 0;
        Payment::where('was_split', true)->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $newCategory = $this->getCategoryFromKostCode($payment->code);

                if ($newCategory != $payment->category) {
                    $payment->update([
                        'category' => $newCategory
                    ]);
                    $count++;
                }
            }
        });

        $this->sendInfoMessage('Исправлено ' . $count . ' категорий оплат из CRM');
        $this->sendInfoMessage('Исправление категорий оплат завершено');

        $this->endProcess();

        return 0;
    }

    private function getCategoryFromKostCode($code)
    {
        return Payment::CATEGORY_RAD;

        $salary = ['7.17', '7.18', '7.26'];
        $provider = [
            '1.1.6', '1.1.7', '1.2.4', '2.1.1', '2.1.10', '2.1.2', '2.1.7', '2.2', '2.2.4', '3', '3.1.2',
            '3.2.2', '4.2', '5.10', '5.2', '7.10', '7.29', '7.32', '7.33', '7.6', '7.7'];
        $transfer = ['7.15'];
        $service = ['5.5', '7.11', '7.21', '7.22', '7.23', '7.24', '7.27', '7.30', '7.31', '7.5', '7.8', '7.7',];

        if (in_array($code, $salary)) {
            return Payment::CATEGORY_SALARY;
        }
        if (in_array($code, $provider)) {
            return Payment::CATEGORY_MATERIAL;
        }
        if (in_array($code, $transfer)) {
            return Payment::CATEGORY_TRANSFER;
        }
        if (in_array($code, $service)) {
            return Payment::CATEGORY_OPSTE;
        }

        return Payment::CATEGORY_RAD;
    }
}
