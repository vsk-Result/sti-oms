<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\PivotObjectDebt;
use App\Services\DebtImport\DebtImportService;
use App\Services\DebtImport\Imports\ProviderImportFromSupply;
use App\Services\PivotObjectDebtService;

class ImportProviderDebtsFromSupplyExcel extends HandledCommand
{
    protected $signature = 'oms:import-provider-debts-from-supply-excel';

    protected $description = 'Загружает долги по поставщикам из Excel (из таблицы Богаевой)';

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

        $import = new ProviderImportFromSupply();

        $this->sendInfoMessage('Попытка загрузить файл ' . $import->getFilename());

        try {
            $data = $this->debtImportService->importDebts($import);
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по поставщика "' . $import->getFilename() . '"');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        if (! empty($data['errors'])) {
            $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по поставщика "' . $import->getFilename() . '"');
            foreach ($data['errors'] as $error) {
                $this->sendErrorMessage($error);
            }
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Файл "' . $import->getFilename() . '" успешно загружен');

        $this->sendInfoMessage('Обновление сводных данных по долгам поставщиков');

        try {
            $this->pivotObjectDebtService->updatePivotDebtInfo(
                $data['data'],
                PivotObjectDebt::DEBT_TYPE_PROVIDER,
                PivotObjectDebt::DEBT_SOURCE_PROVIDER_SUPPLY,
                $import->getSource(),
            );
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось обновить сводные данные долгов по поставщика');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Сводные данные успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
