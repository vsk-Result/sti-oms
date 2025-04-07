<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\PivotObjectDebt;
use App\Services\DebtImport\DebtImportService;
use App\Services\DebtImport\Imports\ContractorImportFrom1C;
use App\Services\DebtImport\Imports\ContractorImportFromClosedObject;
use App\Services\PivotObjectDebtService;

class ImportContractorDebtsFromClosedObjectExcel extends HandledCommand
{
    protected $signature = 'oms:import-contractor-debts-from-closed-object-excel';

    protected $description = 'Загружает долги по подрядчикам для закрытых объектов из Excel';

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

        $import = new ContractorImportFromClosedObject();

        try {
            $data = $this->debtImportService->importDebts($import);
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по подрядчикам "' . $import->getFilename() . '"');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        if (! empty($data['errors'])) {
            $this->sendErrorMessage('Не удалось загрузить файл для импорта долгов по подрядчикам "' . $import->getFilename() . '"');
            foreach ($data['errors'] as $error) {
                $this->sendErrorMessage($error);
            }
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Файл "' . $import->getFilename() . '" успешно загружен');

        $this->sendInfoMessage('Обновление сводных данных по долгам подрядчикам');

        try {
            PivotObjectDebt::where('debt_type_id', PivotObjectDebt::DEBT_TYPE_CONTRACTOR)
                ->where('debt_source_id', PivotObjectDebt::DEBT_SOURCE_CONTRACTOR_CLOSED_OBJECT)
                ->delete();

            $this->pivotObjectDebtService->updatePivotDebtInfo(
                $data['data'],
                PivotObjectDebt::DEBT_TYPE_CONTRACTOR,
                PivotObjectDebt::DEBT_SOURCE_CONTRACTOR_CLOSED_OBJECT,
                $import->getSource(),
            );
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось обновить сводные данные долгов по подрядчикам');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Сводные данные успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
