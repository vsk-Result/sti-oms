<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Debt\DebtImport;
use App\Models\Status;
use App\Services\DebtImportService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ImportServiceDebtsFromExcel extends HandledCommand
{
    protected $signature = 'oms:service-debts-from-excel';

    protected $description = 'Загружает долги по услугам из Excel (из 1С)';

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

        // Путь до файла с автоматической загрузкой
        $importFilePath = storage_path() . '/app/public/public/objects-debts/Uslugi(XLSX).xlsx';

        //Путь до файла с ручной загрузкой
        $importManualFilePath = storage_path() . '/app/public/public/objects-debts-manuals/Uslugi(XLSX).xlsx';

        if (! File::exists($importFilePath)) {
            $this->sendErrorMessage('Файл для загрузки "' . $importFilePath . '" не найден, загружается ручной файл');

            if (! File::exists($importManualFilePath)) {
                $this->sendErrorMessage('Файл для загрузки "' . $importManualFilePath . '" не найден');
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

        $prevImport = DebtImport::where('date', Carbon::now()->format('Y-m-d'))
            ->where('type_id', DebtImport::TYPE_SERVICE_1C)
            ->where('status_id', Status::STATUS_ACTIVE)
            ->first();

        try {
            $importStatus = $this->debtImportService->createImport(['file' => new UploadedFile($importFilePath, 'Uslugi(XLSX).xlsx')], 'service');
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

        if ($prevImport) {
            $prevImport->delete();
        }

        $this->endProcess();

        return 0;
    }
}
