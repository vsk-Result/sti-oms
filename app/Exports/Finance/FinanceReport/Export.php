<?php

namespace App\Exports\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Sheets\ObjectPivotSheet;
use App\Exports\Finance\FinanceReport\Sheets\PivotSheet;
use App\Models\Payment;
use App\Models\Status;
use Carbon\Carbon;
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
        $years = array_reverse(collect($this->info['objectsInfo']->years)->toArray(), true);
        $summary = $this->info['objectsInfo']->summary;

        $sheets = [
            new PivotSheet('Сводная по балансам и долгам', $this->info),
        ];

        foreach ($years as $year => $objects) {
            $sheetName = 'Сводная (' . $year . ')';
            $pivotInfo = [
                'pivot_list' => [
                    'Текущее сальдо' => 'payment_total_balance',
                    'Общие затраты' => 'general_costs_amount',
                    'Промежуточный баланс с текущими долгами и общими расходами компании' => 'interim_balance',
                    'Общая сумма договоров' => 'contract_total_amount',
                    'Остаток денег к получ. с учётом ГУ' => 'contract_avanses_non_closes_amount',
                    'PROM BALANS +  NE ZAKRITI DOGOVOR' => 'interim_balance_non_closes',
                    'Сумма аванса к получению' => 'contract_avanses_left_amount',
                    'Долг подписанных актов' => 'contract_avanses_acts_left_paid_amount',
                    'Всего оплачено авансов' => 'contract_avanses_received_amount',
                    'Всего оплачено по актам' => 'contract_avanses_acts_paid_amount',
                    'Не закрытый аванс' => 'contract_avanses_notwork_left_amount',
                    'Долг гарантийного удержания' => 'contract_avanses_acts_deposites_amount',
                    'Долг подрядчикам' => 'contractor',
                    'Долг за материалы' => 'provider',
                    'Долг за услуги' => 'service',
                    'Долг на зарплаты ИТР' => 'salary_itr',
                    'Долг на зарплаты рабочим' => 'salary_work',
                ],
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
