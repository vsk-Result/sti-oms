<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Object\BObject;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UpdateTranferCosts extends HandledCommand
{
    protected $signature = 'oms:update-transfer-costs';

    protected $description = 'Обновляет затраты по трансферу объектов';

    protected string $period = 'Каждый день в 13 и 18';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        $years = [
            2021,
            2022,
            2023,
            2024,
            2025
        ];

        $periods = [];

        foreach ($years as $year) {
            $periods['years'][] = [
                'name' => $year,
                'quarts' => [
                    [
                        'name' => '1 квартал',
                        'period' => [$year . '-01-01', $year . '-03-31'],
                        'months' => [
                            [
                                'name' => 'Январь',
                                'date_name' => $year . '-01',
                                'period' => [$year . '-01-01', $year . '-01-31']
                            ],
                            [
                                'name' => 'Февраль',
                                'date_name' => $year . '-02',
                                'period' => [$year . '-02-01', $year . '-02-' . Carbon::parse($year . '-02-01')->endOfMonth()->day]
                            ],
                            [
                                'name' => 'Март',
                                'date_name' => $year . '-03',
                                'period' => [$year . '-03-01', $year . '-03-31']
                            ]
                        ]
                    ],
                    [
                        'name' => '2 квартал',
                        'period' => [$year . '-04-01', $year . '-06-30'],
                        'months' => [
                            [
                                'name' => 'Апрель',
                                'date_name' => $year . '-04',
                                'period' => [$year . '-04-01', $year . '-04-30']
                            ],
                            [
                                'name' => 'Май',
                                'date_name' => $year . '-05',
                                'period' => [$year . '-05-01', $year . '-05-31']
                            ],
                            [
                                'name' => 'Июнь',
                                'date_name' => $year . '-06',
                                'period' => [$year . '-06-01', $year . '-06-30']
                            ]
                        ]
                    ],
                    [
                        'name' => '3 квартал',
                        'period' => [$year . '-07-01', $year . '-09-30'],
                        'months' => [
                            [
                                'name' => 'Июль',
                                'date_name' => $year . '-07',
                                'period' => [$year . '-07-01', $year . '-07-31']
                            ],
                            [
                                'name' => 'Август',
                                'date_name' => $year . '-08',
                                'period' => [$year . '-08-01', $year . '-08-31']
                            ],
                            [
                                'name' => 'Сентябрь',
                                'date_name' => $year . '-09',
                                'period' => [$year . '-09-01', $year . '-09-30']
                            ]
                        ]
                    ],
                    [
                        'name' => '4 квартал',
                        'period' => [$year . '-10-01', $year . '-12-31'],
                        'months' => [
                            [
                                'name' => 'Октябрь',
                                'date_name' => $year . '-10',
                                'period' => [$year . '-10-01', $year . '-10-31']
                            ],
                            [
                                'name' => 'Ноябрь',
                                'date_name' => $year . '-11',
                                'period' => [$year . '-11-01', $year . '-11-30']
                            ],
                            [
                                'name' => 'Декабрь',
                                'date_name' => $year . '-12',
                                'period' => [$year . '-12-01', $year . '-12-31']
                            ]
                        ]
                    ]
                ],
            ];
        }

        $prevMonth = Carbon::now()->subMonthsNoOverflow(2)->format('Y-m');
        $transferCacheData = Cache::get('calc_workers_cost_transfer_data', []);

        foreach ($periods['years'] as $year) {
            foreach ($year['quarts'] as $quart) {
                foreach ($quart['months'] as $month) {
                    if (isset($transferCacheData[$month['date_name']])) {
                        if ($month['date_name'] >= $prevMonth) {
                            $transferData = ObjectService::getDistributionTransferServiceByPeriod($month['period']);
                            $transferCacheData[$month['date_name']] = $transferData;
                        }
                    } else {
                        $transferData = ObjectService::getDistributionTransferServiceByPeriod($month['period']);
                        $transferCacheData[$month['date_name']] = $transferData;
                    }
                }
            }
        }

        dd($transferCacheData);

        Cache::put('calc_workers_cost_transfer_data', $transferCacheData);

        $this->sendInfoMessage('Данные успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
