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
                    'Приходы' => 'receive',
                    'Расходы' => 'pay',
                    'Сальдо без общ. Расходов' => 'balance',
                    'Общие расходы' => 'general_balance',
                    'Сальдо c общ. Расходами' => 'balance_with_general_balance',
                    'Долг подрядчикам' => 'contractor_debt',
                    'Долг подрядчикам за ГУ' => 'contractor_debt_gu',
                    'Долг поставщикам' => 'provider_debt',
                    'Долг за услуги' => 'service_debt',
                    'Долг на зарплаты ИТР' => 'itr_salary_debt',
                    'Долг на зарплаты рабочим' => 'workers_salary_debt',
                    'Долг Заказчика за выпол.работы' => 'dolgZakazchikovZaVipolnenieRaboti',
                    'Долг Заказчика за ГУ (фактич.удерж.)' => 'dolgFactUderjannogoGU',
                    'Текущий Баланс объекта' => 'objectBalance',
                    'Сумма договоров с Заказчиком' => 'contractsTotalAmount',
                    'Остаток неотработанного аванса' => 'ostatokNeotrabotannogoAvansa',
                    'Остаток к получ. от заказчика (в т.ч. ГУ)' => 'ostatokPoDogovoruSZakazchikom',
                    'Прогнозируемый Баланс объекта' => 'prognozBalance',
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
