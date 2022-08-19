<?php

namespace App\Exports\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Sheets\ObjectPivotSheet;
use App\Exports\Finance\FinanceReport\Sheets\ObjectSheet;
use App\Exports\Finance\FinanceReport\Sheets\PivotSheet;
use App\Models\Object\BObject;
use App\Services\Contract\ContractService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $info;

    private ContractService $contractService;

    public function __construct(array $info, ContractService $contractService)
    {
        $this->info = $info;
        $this->contractService = $contractService;
    }

    public function sheets(): array
    {
        $sheets = [
            new PivotSheet('Сводная по балансам и долгам', $this->info),
            new ObjectPivotSheet('Сводная по объектам', $this->contractService),
        ];

        $objects = BObject::whereIn('code', ['288', '317', '325', '332', '338', '342', '343', '344', '346', '349', '352', '353', '354', '358', '359'])
            ->orderByDesc('code')
            ->get();

        foreach ($objects as $object) {
            $sheets[] = new ObjectSheet($object->getName(), $this->contractService, $object);
        }

        return $sheets;
    }
}
