<?php

namespace App\Console\Commands;

use App\Services\DebtImportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportDTDebtsFromExcel extends Command
{
    protected $signature = 'oms-imports:dt-debts-from-excel';

    protected $description = 'Загружает долги по ДТ Термо из Excel';

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
        Log::channel('custom_imports_log')->debug('[START] Загрузка долгов по ДТ Термо из Excel');

        $importFilePath = storage_path() . '/app/public/public/objects-debts/DT.xlsx';

        if (! File::exists($importFilePath)) {
            Log::channel('custom_imports_log')->debug('[ERROR] Файл для загрузки "' . $importFilePath . '" не найден');
            return 0;
        }

        try {
            $importStatus = $this->debtImportService->createImport([
                'date' => Carbon::now()->format('Y-m-d'),
                'company_id' => 1,
                'file' => new UploadedFile($importFilePath, 'DT.xlsx')
            ]);
        } catch (\Exception $e) {
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось загрузить файл: "' . $e->getMessage());
            return 0;
        }

        if ($importStatus !== 'ok') {
            Log::channel('custom_imports_log')->debug('[ERROR] Не удалось загрузить файл: "' . $importStatus);
            return 0;
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Файл успешно загружен');

        return 0;
    }
}
