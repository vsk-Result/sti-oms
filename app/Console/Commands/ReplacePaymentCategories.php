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

        Log::channel('custom_imports_log')->debug('[INFO] Обработка RAD');
        Log::channel('custom_imports_log')->debug('[INFO] Найдено ' . Payment::where('category', 'RAD')->count() . ' оплат');

        try {
            Payment::where('category', 'RAD')->update(['category' => 'Работы']);
        } catch(Exception $e){
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось обновить категорию RAD: "' . $e->getMessage());
        }

        Log::channel('custom_imports_log')->debug('[INFO] Обработка MATERIAL');
        Log::channel('custom_imports_log')->debug('[INFO] Найдено ' . Payment::where('category', 'MATERIAL')->count() . ' оплат');

        try {
            Payment::where('category', 'MATERIAL')->update(['category' => 'Материалы']);
        } catch(Exception $e){
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось обновить категорию MATERIAL: "' . $e->getMessage());
        }

        Log::channel('custom_imports_log')->debug('[INFO] Обработка OPSTE');
        Log::channel('custom_imports_log')->debug('[INFO] Найдено ' . Payment::where('category', 'OPSTE')->count() . ' оплат');

        try {
            Payment::where('category', 'OPSTE')->update(['category' => 'Накладные/Услуги']);
        } catch(Exception $e){
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось обновить категорию OPSTE: "' . $e->getMessage());
        }

        $this->CRONProcessService->successProcess($this->signature);
        Log::channel('custom_imports_log')->debug('[SUCCESS] Обработка завершена');

        return 0;
    }
}
