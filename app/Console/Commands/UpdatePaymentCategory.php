<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\CRONProcessService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePaymentCategory extends Command
{
    protected $signature = 'oms:update-payment-category';

    protected $description = 'Исправляет категории оплат и заполняет пустые';

    private PaymentService $paymentService;

    public function __construct(CRONProcessService $CRONProcessService, PaymentService $paymentService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Вручную'
        );
        $this->paymentService = $paymentService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Исправляет категории оплат и заполняет пусты');

        Log::channel('custom_imports_log')->debug('[INFO] Старт исправления категорий оплат');

        Log::channel('custom_imports_log')->debug('[INFO] Заполнение пустых категорий');

        $this->paymentService->loadCategoriesList();

        $count = 0;
        Payment::where(function($q) {
            $q->whereNull('category');
            $q->orWhere('category', '');
        })->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $category = $this->paymentService->findCategoryFromDescription($payment['description']);

                if (is_null($category)) {
                    continue;
                }

                $payment->update([
                    'category' => $category
                ]);
                $count++;
            }
        });

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправлено ' . $count . ' оплат с пустой категорией');

        Log::channel('custom_imports_log')->debug('[INFO] Исправление некорректных категорий');

        $count = 0;
//        Payment::where(function($q) {
//            $q->whereNull('category');
//            $q->orWhere('category', '');
//        })->chunk(1000, function($payments) use (&$count){
//            foreach ($payments as $payment) {
//                $category = $this->paymentService->findCategoryFromDescription($payment['description']);
//
//                if (is_null($category)) {
//                    continue;
//                }
//
//                $payment->update([
//                    'category' => $category
//                ]);
//                $count++;
//            }
//        });

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправлено ' . $count . ' оплат с некорректной категорией');

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправление категорий оплатах завершено');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
