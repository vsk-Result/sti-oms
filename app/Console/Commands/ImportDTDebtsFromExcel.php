<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\DebtImportService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ImportDTDebtsFromExcel extends HandledCommand
{
    protected $signature = 'oms:dt-debts-from-excel';

    protected $description = 'Загружает долги по ДТ Термо из Excel';

    protected string $period = 'Ежедневно в 19:30';

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

        // Путь до файла с автоматической загрузкой
        $importFilePath = storage_path() . '/app/public/public/objects-debts/DT.xlsx';

        //Путь до файла с ручной загрузкой
        $importManualFilePath = storage_path() . '/app/public/public/objects-debts-manuals/DT.xlsx';

        if (! File::exists($importFilePath)) {
            $this->sendInfoMessage('Файл для загрузки "' . $importFilePath . '" не найден, загружается ручной файл');

            if (! File::exists($importManualFilePath)) {
                $this->sendInfoMessage('Файл для загрузки "' . $importManualFilePath . '" не найден');
                $this->endProcess();
                return 0;
            }
        }

        // Самый новый файл будет являться актуальным при загрузке
        if (! File::exists($importFilePath)) {
            $importFilePath = $importManualFilePath;
        } else {
            if (File::exists($importManualFilePath)) {
                if (File::lastModified($importManualFilePath) > File::lastModified($importFilePath)) {
                    $importFilePath = $importManualFilePath;
                }
            }
        }

        try {
            $importStatus = $this->debtImportService->createImport([
                'date' => Carbon::now()->format('Y-m-d'),
                'company_id' => 1,
                'file' => new UploadedFile($importFilePath, 'DT.xlsx')
            ]);
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
