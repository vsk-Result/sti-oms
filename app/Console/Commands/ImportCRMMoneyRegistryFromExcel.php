<?php

namespace App\Console\Commands;

use App\Services\CRM\MoneyRegistryService;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

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
            'Каждый час'
        );
        $this->moneyRegistryService = $moneyRegistryService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Загрузка зарплатных реестров в CRM из Excel');

        Log::channel('custom_imports_log')->debug('[INFO] Попытка разорхивировать файл с реестрами Payments.zip');

        $zip = new ZipArchive();
        $status = $zip->open(storage_path('app/public/public/objects-debts-manuals/Payments.zip'));
        if ($status !== true) {
            $errorMessage = '[ERROR] Не удалось разорхивировать файл Payments.zip: ' . $status;
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
        }

        try {
            $zip->extractTo(storage_path('app/public/public/crm-registry/'));
            $zip->close();
        } catch (\Exception $e) {
            $errorMessage = '[ERROR] Не удалось разорхивировать файл Payments.zip: ' . $e->getMessage();
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Рахорхивирование прошло успешно');

        $importedCount = 0;
        $files = Storage::files('public/crm-registry');

        foreach ($files as $file) {
            $fileName = File::name($file);
            $extension = File::extension($file);

            if (str_contains($fileName, 'imported')) {
                Log::channel('custom_imports_log')->debug('[INFO] Файл "' . $fileName . '.' . $extension . '" содержит imported. Файл перемещен в imported.');
                Storage::move('public/crm-registry/' . $fileName . '.' . $extension, 'public/crm-registry/imported/' . $fileName . '.' . $extension);
                continue;
            }

            try {
                if ($this->moneyRegistryService->isRegistryWasImported($file)) {
                    Log::channel('custom_imports_log')->debug('[INFO] Файл "' . $fileName . '.' . $extension . '" ранее уже был подгружен. Файл перемещен в imported.');
                    Storage::move('public/crm-registry/' . $fileName . '.' . $extension, 'public/crm-registry/imported/' . $fileName . '_imported.' . $extension);
                    continue;
                }

                $this->moneyRegistryService->importRegistry($file);
                $importedCount++;
                Storage::move('public/crm-registry/' . $fileName . '.' . $extension, 'public/crm-registry/imported/' . $fileName . '_imported.' . $extension);

                Log::channel('custom_imports_log')->debug('[INFO] Файл ' . $fileName . '.' . $extension . ' успешно загружен');
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
