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
        $salary = ['7.9', '7.8', '7.9.1', '7.9.2', '7.8.1', '7.8.2'];
        $provider = [
            '1.1.6', '2.1.1', '2.1.2', '2.1.6', '2.2.1', '3.1.1',
            '3.2.2', '4.2', '5.8', '5.1', '7.24', '7.31', '7.32', '7.28', '7.30', '7.12'];
        $transfer = ['7.11'];
        $service = ['5.5', '7.21', '5.11', '5.11.1', '5.11.2', '8.2', '5.13', '7.18', '5.14', '5.14.1', '5.14.2', '7.27', '5.2', '7.26', '7.12'];

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
