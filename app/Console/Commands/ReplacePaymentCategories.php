<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReplacePaymentCategories extends Command
{
    protected $signature = 'oms:replace-payment-categories';

    protected $description = 'Заменяет старые категории оплат на новые';

    public function __construct(CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Разово в консоли'
        );
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Замена старых категории оплат на новые');

        $categories = [
            'Услуги' => Payment::CATEGORY_OPSTE,
            'Подрядчики' => Payment::CATEGORY_RAD,
            'Поставщики' => Payment::CATEGORY_MATERIAL,
        ];

        foreach ($categories as $old => $new) {
            Log::channel('custom_imports_log')->debug('[INFO] Обработка "' . $old . '"');
            Log::channel('custom_imports_log')->debug('[INFO] Найдено ' . Payment::where('category', $old)->count() . ' оплат');

            try {
                Payment::where('category', $old)->update(['category' => $new]);
            } catch(Exception $e){
                Log::channel('custom_imports_log')->debug('[ERROR] Не удалось обновить категорию "' . $old . '": "' . $e->getMessage());
            }
        }

        $this->CRONProcessService->successProcess($this->signature);
        Log::channel('custom_imports_log')->debug('[SUCCESS] Обработка завершена');

        return 0;
    }
}
