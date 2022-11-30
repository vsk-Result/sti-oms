<?php

namespace App\Console\Commands;

use App\Services\DebtImportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportContractorDebtsFromExcel extends Command
{
    protected $signature = 'oms-imports:contractor-debts-from-excel';

    protected $description = 'Загружает долги по подрядчикам из Excel (из 1С)';

    private DebtImportService $debtImportService;

    public function __construct(DebtImportService $debtImportService)
    {
        parent::__construct();
        $this->debtImportService = $debtImportService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Загрузка долгов по подрядчикам из Excel (из 1С)');

        $importFilePath = storage_path() . '/app/public/public/TabelOMS_(XLSX).xlsx';

        if (! File::exists($importFilePath)) {
            Log::channel('custom_imports_log')->debug('[ERROR] Файл для загрузки "' . $importFilePath . '" не найден');
            return 0;
        }

        try {
            $importStatus = $this->debtImportService->createImport(['file' => new UploadedFile($importFilePath, 'TabelOMS_(XLSX).xlsx')]);
        } catch (\Exception $e) {
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось загрузить файл: "' . $e->getMessage());
            return 0;
        }

        if ($importStatus !== 'ok') {
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось загрузить файл: "' . $importStatus);
            return 0;
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Файл успешно загружен                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ');

        return 0;
    }
}
