<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\DebtImportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ImportContractorDebtsFromExcel extends HandledCommand
{
    protected $signature = 'oms:contractor-debts-from-excel';

    protected $description = 'Загружает долги по поставщикам из Excel (из 1С)';

    protected string $period = 'Ежедневно в 19:00';

    public function __construct(private DebtImportService $debtImportService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        //Путь до файла с ручной загрузкой
        $importFilePath = storage_path() . '/app/public/public/objects-debts-manuals/TabelOMS_(XLSX).xlsx';

        if (! File::exists($importFilePath)) {
            $this->sendErrorMessage('Файл для загрузки "' . $importFilePath . '" не найден');
            $this->endProcess();
            return 0;
        }

        try {
            $importStatus = $this->debtImportService->createImport(['file' => new UploadedFile($importFilePath, 'TabelOMS_(XLSX).xlsx')]);
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить файл: "' . $e->getMessage());
            $this->endProcess();
            return 0;
        }

        if ($importStatus !== 'ok') {
            $this->sendErrorMessage('Не удалось загрузить файл: "' . $importStatus);
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Файл успешно загружен');

        $this->endProcess();

        return 0;
    }
}
