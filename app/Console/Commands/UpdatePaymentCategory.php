<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Payment;
use App\Services\PaymentService;

class UpdatePaymentCategory extends HandledCommand
{
    protected $signature = 'oms:update-payment-category';

    protected $description = 'Исправляет категории оплат и заполняет пустые';

    protected string $period = 'Вручную';

    public function __construct(private PaymentService $paymentService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $this->sendInfoMessage('Старт исправления категорий оплат');
        $this->sendInfoMessage('Заполнение пустых категорий');

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

        $this->sendInfoMessage('Исправлено ' . $count . ' оплат с пустой категорией');
        $this->sendInfoMessage('Исправление некорректных категорий');

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

        $this->sendInfoMessage('Исправлено ' . $count . ' оплат с некорректной категорией');
        $this->sendInfoMessage('Исправление категорий оплатах завершено');

        $this->endProcess();

        return 0;
    }
}
