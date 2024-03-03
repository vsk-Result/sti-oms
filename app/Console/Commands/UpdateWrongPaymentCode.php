<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateWrongPaymentCode extends Command
{
    protected $signature = 'oms:update-wrong-payment-code';

    protected $description = 'Исправляет некорректные статьи затрат в оплатах';


    public function __construct(CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 20:00'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Исправление некорректных статей затрат в оплатах');

        Log::channel('custom_imports_log')->debug('[INFO] Старт исправления оплат с запятой');

        $count = 0;
        Payment::where('code', 'LIKE', '%,%')->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $payment->update([
                    'code' => str_replace(',', '.', $payment->code)
                ]);
                $count++;
            }
        });

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправлено ' . $count . ' оплат с запятой');

        $count = 0;
        Payment::where('code', 'LIKE', '%..%')->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $payment->update([
                    'code' => str_replace('..', '.', $payment->code)
                ]);
                $count++;
            }
        });

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправлено ' . $count . ' оплат с двумя точками');

        $count = 0;
        Payment::where('code', 'LIKE', '% %')->chunk(1000, function($payments) use (&$count){
            foreach ($payments as $payment) {
                $payment->update([
                    'code' => str_replace(' ', '', $payment->code)
                ]);
                $count++;
            }
        });

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправлено ' . $count . ' оплат с пробелами');

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

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправлено ' . $count . ' оплат с последней точкой');

        Log::channel('custom_imports_log')->debug('[SUCCESS] Исправление статей затрат в оплатах завершено');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
