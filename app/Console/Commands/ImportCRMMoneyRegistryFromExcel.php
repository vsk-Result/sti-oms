<?php

namespace App\Console\Commands;

use App\Services\CRM\MoneyRegistryService;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportCRMMoneyRegistryFromExcel extends Command
{
    protected $signature = 'oms:import-crm-money-registry-from-excel';

    protected $description = 'Загружает зарплатные реестры в CRM из Excel';

    private MoneyRegistryService $moneyRegistryService;

    public function __construct(MoneyRegistryService $moneyRegistryService, CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 19:00'
        );
        $this->moneyRegistryService = $moneyRegistryService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Загрузка зарплатных реестров в CRM из Excel');

        $importedCount = 0;
        $files = Storage::allFiles('public/crm-registry');

        foreach ($files as $file) {
            $fileName = File::name($file);

            if (str_contains($fileName, 'imported')) {
                Storage::move('public/crm-registry/' . $fileName . '.xls', 'public/crm-registry/imported/' . $fileName . '.xls');
                continue;
            }

            try {
                if ($this->moneyRegistryService->isRegistryWasImported($file)){
                    Storage::move('public/crm-registry/' . $fileName . '.xls', 'public/crm-registry/imported/' . $fileName . '_imported.xls');
                    continue;
                }

                $this->moneyRegistryService->importRegistry($file);
                $importedCount++;
                Storage::move('public/crm-registry/' . $fileName . '.xls', 'public/crm-registry/imported/' . $fileName . '_imported.xls');

                Log::channel('custom_imports_log')->debug('[SUCCESS] Файл ' . $fileName . ' успешно загружен');
            } catch (\Exception $e) {
                $errorMessage = '[ERROR] Не удалось загрузить файл ' . $fileName . ' - ' . $e->getMessage();
                Log::channel('custom_imports_log')->debug($errorMessage);
                $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
                return 0;
            }
        }

        $this->CRONProcessService->successProcess($this->signature);

        if ($importedCount === 0) {
            Log::channel('custom_imports_log')->debug('[SUCCESS] Файлы для загрузки не обнаружены');
        } else {
            Log::channel('custom_imports_log')->debug('[SUCCESS] Файлы успешно загружены');
        }

        return 0;
    }
}
