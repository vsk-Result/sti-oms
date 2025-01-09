<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use Illuminate\Support\Facades\Cache;

class UpdateGeneralCosts extends HandledCommand
{
    protected $signature = 'oms:update-general-costs';

    protected $description = 'Обновляет общие затраты объектов';

    protected string $period = 'Каждые 30 минут';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $periodsByYears = [
                '2017' => [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-12-31',
                        'bonus' => 0,
                    ],
                ],
                '2018' => [
                    [
                        'start_date' => '2018-01-01',
                        'end_date' => '2018-12-31',
                        'bonus' => 21421114,
                    ],
                ],
                '2019' => [
                    [
                        'start_date' => '2019-01-01',
                        'end_date' => '2019-12-31',
                        'bonus' => (39760000 + 692048),
                    ],
                ],
                '2020' => [
                    [
                        'start_date' => '2020-01-01',
                        'end_date' => '2020-12-31',
                        'bonus' => (2000000 + 418000 + 1615000),
                    ],
                ],
                '2021' => [
                    [
                        'start_date' => '2021-01-01',
                        'end_date' => '2021-03-02',
                        'bonus' => 600000,
                    ],
                    [
                        'start_date' => '2021-03-03',
                        'end_date' => '2021-12-31',
                        'bonus' => (600000 + 68689966),
                    ],
                ],
                '2022' => [
                    [
                        'start_date' => '2022-01-01',
                        'end_date' => '2022-10-11',
                        'bonus' => 0,
                    ],
                    [
                        'start_date' => '2022-10-12',
                        'end_date' => '2022-12-31',
                        'bonus' => 0,
                    ],
                ],
                '2023' => [
                    [
                        'start_date' => '2023-01-01',
                        'end_date' => '2023-07-20',
                        'bonus' => 0,
                    ],
                    [
                        'start_date' => '2023-07-21',
                        'end_date' => '2023-11-28',
                        'bonus' => 0,
                    ],
                    [
                        'start_date' => '2023-11-29',
                        'end_date' => '2023-12-31',
                        'bonus' => 0,
                    ]
                ],
                '2024' => [
                    [
                        'start_date' => '2024-01-01',
                        'end_date' => '2024-12-31',
                        'bonus' => 0,
                    ],
                ],
            ];


            $object27_1 = BObject::where('code', '27.1')->first();

            $periodsByYears = array_reverse($periodsByYears, true);

            $generalInfo = [];
            $groupedByYearsInfo = [];
            $generalTotalAmount = 0;

            foreach ($periodsByYears as $year => $periods) {
                $groupedByYearsInfo[$year]['total'] = [
                    'cuming_amount' => 0,
                    'general_amount' => 0,
                ];
                foreach ($periods as $index => $period) {
                    $datesBetween = [$period['start_date'], $period['end_date']];
                    $paymentQuery = \App\Models\Payment::query()->whereBetween('date', $datesBetween)->whereIn('company_id', [1, 5]);
                    $generalAmount = (clone $paymentQuery)->where('code', '!=', '7.11')->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount')
                        + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount')
                        + $period['bonus'];

                    $generalInfo[$year][$index] = [
                        'start_date' => $period['start_date'],
                        'end_date' => $period['end_date'],
                        'general_amount' => $generalAmount,
                        'info' => \App\Services\ObjectService::getGeneralCostsByPeriod($period['start_date'], $period['end_date'], $period['bonus']),
                    ];

                    $generalTotalAmount += $generalAmount;

                    foreach ($generalInfo[$year][$index]['info'] as $objectId => $i) {
                        if (!isset($groupedByYearsInfo[$year][$objectId]['cuming_amount'])) {
                            $groupedByYearsInfo[$year][$objectId]['cuming_amount'] = 0;
                        }
                        if (!isset($groupedByYearsInfo[$year][$objectId]['general_amount'])) {
                            $groupedByYearsInfo[$year][$objectId]['general_amount'] = 0;
                        }

                        $groupedByYearsInfo[$year][$objectId]['cuming_amount'] += $i['cuming_amount'];
                        $groupedByYearsInfo[$year][$objectId]['general_amount'] += $i['general_amount'];
                    }

                    foreach ($generalInfo[$year][$index]['info'] as $i) {
                        $groupedByYearsInfo[$year]['total']['cuming_amount'] += $i['cuming_amount'];
                    }

                    $groupedByYearsInfo[$year]['total']['general_amount'] += $generalAmount;
                }
            }

            $info = [
                'generalInfo' => $generalInfo,
                'groupedByYearsInfo' => $groupedByYearsInfo,
                'generalTotalAmount' => $generalTotalAmount,
            ];

            Cache::put('general_costs', $info);

        } catch (\Exception $e) {
            $this->sendErrorMessage('Ошибка в вычислениях: ' . $e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Данные успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
