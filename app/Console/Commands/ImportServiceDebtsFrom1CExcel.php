<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\PivotObjectDebt;
use App\Services\DebtImport\DebtImportService;
use App\Services\DebtImport\Imports\ServiceImportFrom1C;
use App\Services\PivotObjectDebtService;

class ImportServiceDebtsFrom1CExcel extends HandledCommand
{
    protected $signature = 'oms:import-service-debts-from-1c-excel';

    protected $description = 'Загружает долги по услугам из Excel (из 1С)';

    protected string $period = 'Вручную';

    public function __construct(
        private DebtImportService $debtImportService,
        private PivotObjectDebtService $pivotObjectDebtService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $import = new ServiceImportFrom1C();

        try {
            $data = $this->debtImportService->importDebts($import);
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по услугам "' . $import->getFilename() . '"');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        if (! empty($data['errors'])) {
            $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по услугам "' . $import->getFilename() . '"');
            foreach ($data['errors'] as $error) {
                $this->sendErrorMessage($error);
            }
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Файл "' . $import->getFilename() . '" успешно загружен');

        $this->sendInfoMessage('Обновление сводных данных по долгам за услуги');

        try {
            $this->pivotObjectDebtService->updatePivotDebtInfo(
                $data['data'],
                PivotObjectDebt::DEBT_TYPE_SERVICE,
                PivotObjectDebt::DEBT_SOURCE_SERVICE_1C,
                $import->getSource(),
            );
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось обновить сводные данные долгов по услугам');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Сводные данные успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
