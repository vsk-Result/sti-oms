<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\PivotObjectDebt;
use App\Services\DebtImport\DebtImportService;
use App\Services\DebtImport\Imports\ManualReplaceImport;
use App\Services\PivotObjectDebtService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImportManualReplaceDebtsFromExcel extends HandledCommand
{
    protected $signature = 'oms:import-manual-replace-debts-from-excel';

    protected $description = 'Загружает долги по подрядчикам, поставщикам, за услуги из Excel для замены долгов вручную';

    protected string $period = 'Вручную';

    public function __construct(
        private DebtImportService $debtImportService,
        private PivotObjectDebtService $pivotObjectDebtService
    ) {
        parent::__construct();
    }

    public function handle()
    {
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        $files = Storage::files('public/objects-debts-manuals');

        foreach ($files as $file) {
            $fileName = File::name($file);
            $extension = File::extension($file);

            if (! str_contains($fileName, '__manual_replace') || $fileName === 'example__manual_replace') {
                continue;
            }

            $objectCode = mb_substr($fileName, 0, mb_strpos($fileName, '__manual_replace'));

            $import = new ManualReplaceImport($fileName . '.' . $extension, $objectCode);

            $this->sendInfoMessage('Попытка загрузить файл ' . $import->getFilename());

            try {
                $data = $this->debtImportService->importDebts($import);
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по подрядчикам, поставщикам, за услуги "' . $import->getFilename() . '"');
                $this->sendErrorMessage($e->getMessage());
                $this->endProcess();
                return 0;
            }

            if (! empty($data['errors'])) {
                $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по подрядчикам, поставщикам, за услуги "' . $import->getFilename() . '"');
                foreach ($data['errors'] as $error) {
                    $this->sendErrorMessage($error);
                }
                $this->endProcess();
                return 0;
            }

            $this->sendInfoMessage('Файл "' . $import->getFilename() . '" успешно загружен');

            $this->sendInfoMessage('Обновление сводных данных по долгам подрядчикам');

            try {
                $this->pivotObjectDebtService->updatePivotDebtInfo(
                    $data['contractor']['data'],
                    PivotObjectDebt::DEBT_TYPE_CONTRACTOR,
                    PivotObjectDebt::DEBT_SOURCE_CONTRACTOR_MANUAL,
                    $import->getSource(),
                );
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось обновить сводные данные долгов по подрядчикам');
                $this->sendErrorMessage($e->getMessage());
                $this->endProcess();
                return 0;
            }

            $this->sendInfoMessage('Сводные данные по подрядчикам успешно обновлены');

            $this->sendInfoMessage('Обновление сводных данных по долгам поставщикам');

            try {
                $this->pivotObjectDebtService->updatePivotDebtInfo(
                    $data['provider']['data'],
                    PivotObjectDebt::DEBT_TYPE_PROVIDER,
                    PivotObjectDebt::DEBT_SOURCE_PROVIDER_MANUAL,
                    $import->getSource(),
                );
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось обновить сводные данные долгов по поставщикам');
                $this->sendErrorMessage($e->getMessage());
                $this->endProcess();
                return 0;
            }

            $this->sendInfoMessage('Сводные данные по поставщикам успешно обновлены');

            $this->sendInfoMessage('Обновление сводных данных по долгам за услуги');

            try {
                $this->pivotObjectDebtService->updatePivotDebtInfo(
                    $data['service']['data'],
                    PivotObjectDebt::DEBT_TYPE_SERVICE,
                    PivotObjectDebt::DEBT_SOURCE_SERVICE_MANUAL,
                    $import->getSource(),
                );
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось обновить сводные данные долгов за услуги');
                $this->sendErrorMessage($e->getMessage());
                $this->endProcess();
                return 0;
            }

            $this->sendInfoMessage('Сводные данные за услуги успешно обновлены');
        }

        $this->endProcess();

        return 0;
    }
}
