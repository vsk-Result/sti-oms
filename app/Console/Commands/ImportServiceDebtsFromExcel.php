<?php

namespace App\Console\Commands;

use App\Services\CRONProcessService;
use App\Services\DebtImportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportServiceDebtsFromExcel extends Command
{
    protected $signature = 'oms:service-debts-from-excel';

    protected $description = 'Загружает долги по услугам из Excel (из 1С)';

    private DebtImportService $debtImportService;

    public function __construct(DebtImportService $debtImportService, CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 19:00'
        );
        $this->debtImportService = $debtImportService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Загрузка долгов по услугам из Excel (из 1С)');

        $importFilePath = storage_path() . '/app/public/public/objects-debts/Uslugi(XLSX).xlsx';

        if (! File::exists($importFilePath)) {
            $errorMessage = '[ERROR] Файл для загрузки "' . $importFilePath . '" не найден';
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        try {
            $importStatus = $this->debtImportService->createImport(['file' => new UploadedFile($importFilePath, 'Uslugi(XLSX).xlsx')], 'service');
        } catch (\Exception $e) {
            $errorMessage = '[ERROR] Не удалось загрузить файл: "' . $e->getMessage();
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        if ($importStatus !== 'ok') {
            $errorMessage = '[ERROR] Не удалось загрузить файл: "' . $importStatus;
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Файл успешно загружен');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
