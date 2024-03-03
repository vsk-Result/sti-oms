<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateSplitPaymentCategoryFromCode extends Command
{
    protected $signature = 'oms:update-split-payment-category-from-code';

    protected $description = 'Исправляет категории в оплатах, разбитых из CRM на основе статей зартат';


    public function __construct(CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Вручную'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Исправление категорий в оплатах, разбитых из CRM на основе статей зартат');

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

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправлено ' . $count . ' категорий оплат из CRM');

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправление категорий оплат завершено');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }

    private function getCategoryFromKostCode($code)
    {
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
