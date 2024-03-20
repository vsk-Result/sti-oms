<?php

namespace App\Exports\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Sheets\ObjectPivotSheet;
use App\Exports\Finance\FinanceReport\Sheets\PivotSheet;
use App\Models\FinanceReport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $info;

    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function sheets(): array
    {
        $total = $this->info['objectsInfo']->total;
        $years = collect($this->info['objectsInfo']->years)->toArray();
        $summary = $this->info['objectsInfo']->summary;

        unset($years['Не отображать']);
        unset($years['Общие']);
        unset($years['Удаленные']);

        $sheets = [
            new PivotSheet('Сводная по балансам и долгам', $this->info),
        ];

        foreach ($years as $year => $objects) {
            $sheetName = 'Сводная (' . $year . ')';
            $pivotInfo = [
                'pivot_list' => FinanceReport::getInfoFields(),
                'pivot_info' => compact('total', 'summary'),
                'objects' => $objects
            ];

            $sheets[] = new ObjectPivotSheet($sheetName, $pivotInfo, $year);
        }

//        $objects = BObject::active()
//            ->orderByDesc('code')
//            ->get();
//
//        foreach ($objects as $object) {
//            $sheets[] = new ObjectSheet($object->getName(), $this->contractService, $object);
//        }

        return $sheets;
    }
}
