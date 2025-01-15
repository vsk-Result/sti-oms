<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use App\Services\DebtImport\DebtImportService;
use App\Services\DebtImport\Imports\ContractorImportFromManager;
use App\Services\PivotObjectDebtService;

class ImportContractorDebtsFromManagerExcel extends HandledCommand
{
    protected $signature = 'oms:import-contractor-debts-from-manager-excel';

    protected $description = 'Загружает долги по подрядчикам из Excel (из таблицы фин. менеджера)';

    protected string $period = 'Вручную и в 19:00';

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

        $objectCodesToImport = BObject::getCodesForContractorImportDebts();

        foreach ($objectCodesToImport as $objectCode) {
            if ($objectCode === '000') {
                continue;
            }

            $import = new ContractorImportFromManager($objectCode . '.xlsx', $objectCode);

            $this->sendInfoMessage('Попытка загрузить файл ' . $import->getFilename());

            try {
                $data = $this->debtImportService->importDebts($import);
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по подрядчикам "' . $import->getFilename() . '"');
                $this->sendErrorMessage($e->getMessage());

                continue;
            }

            if (! empty($data['errors'])) {
                $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по подрядчикам "' . $import->getFilename() . '"');
                foreach ($data['errors'] as $error) {
                    $this->sendErrorMessage($error);
                }

                continue;
            }

            $this->sendInfoMessage('Файл "' . $import->getFilename() . '" успешно загружен');

            $this->sendInfoMessage('Обновление сводных данных по долгам подрядчикам');

            try {
                $this->pivotObjectDebtService->updatePivotDebtInfo(
                    $data['data'],
                    PivotObjectDebt::DEBT_TYPE_CONTRACTOR,
                    PivotObjectDebt::DEBT_SOURCE_CONTRACTOR_MANAGER,
                    $import->getSource(),
                );
            } catch (\Exception $e) {
                $this->sendErrorMessage('Не удалось обновить сводные данные долгов по подрядчикам');
                $this->sendErrorMessage($e->getMessage());
                $this->endProcess();
                return 0;
            }

            $this->sendInfoMessage('Сводные данные успешно обновлены');
        }

        $this->endProcess();

        return 0;
    }
}
