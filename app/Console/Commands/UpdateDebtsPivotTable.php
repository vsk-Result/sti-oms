<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\PivotObjectDebtService;

class UpdateDebtsPivotTable extends HandledCommand
{
    protected $signature = 'oms:update-debts-pivot-table';

    protected $description = 'Обновляет сводную таблицу по долгам объектов (поставщики, подрячики, услуги)';

    protected string $period = 'Каждые 10 минут';

    public function __construct(private PivotObjectDebtService $pivotObjectDebtService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $this->pivotObjectDebtService->updatePivotDebtForAllObjects();

        $this->sendInfoMessage('Данные успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
