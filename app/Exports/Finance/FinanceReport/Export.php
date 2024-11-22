<?php

namespace App\Exports\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Sheets\ObjectPivotSheet;
use App\Exports\Finance\FinanceReport\Sheets\PivotSheet;
use App\Models\FinanceReport;
use App\Services\Contract\ActService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $info;
    private array $config;

    public function __construct(array $info, array $config)
    {
        $this->info = $info;
        $this->config = $config;
    }

    public function sheets(): array
    {
        $total = $this->info['objectsInfo']->total;
        $years = collect($this->info['objectsInfo']->years)->toArray();
        $summary = $this->info['objectsInfo']->summary;

        unset($years['Не отображать']);
        unset($years['Общие']);
        unset($years['Удаленные']);

        if (isset($this->config['show_closed_objects'])){
            if (!$this->config['show_closed_objects']) {
                unset($years['Закрытые']);
            }
        }

        $sheets = [
            new PivotSheet('Сводная по балансам и долгам', $this->info),
        ];

        $years = array_merge(['Свод' => $years['Активные']], $years);
        foreach ($years as $y => $objects) {
            $sheetName = $y;
            $pivotInfo = [
                'pivot_list' => FinanceReport::getInfoFields(),
                'pivot_info' => compact('total', 'summary'),
                'objects' => $objects
            ];

            if ($y === 'Свод') {
                $year = 'Активные';
            }

            if ($y === 'Закрытые') {
                $year = 'Закрытые';
            }

            $sheets[] = new ObjectPivotSheet($sheetName, $pivotInfo, $year);
        }

        $sheets[] = new \App\Exports\Pivot\ActCategory\Sheets\PivotSheet($this->config['act_service']);

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
