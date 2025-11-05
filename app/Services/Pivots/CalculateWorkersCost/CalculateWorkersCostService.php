<?php

namespace App\Services\Pivots\CalculateWorkersCost;

use App\Models\AccruedTax\AccruedTax;
use App\Models\Contract\Contract;
use App\Models\CRM\CObject;
use App\Models\CRM\Workhour;
use App\Models\Object\BObject;
use App\Models\CRM\Payment as CRMPayment;
use App\Models\Payment;
use App\Models\SERVICE\WorkhourPivot;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class CalculateWorkersCostService
{
    const COMPANY_GROUPS = [
        'ФОТ рабочие' => 'workers_salary',
        'ФОТ офис + ФОТ ИТР' => 'itr_salary',
        'НДФЛ + страховые взносы' => 'ndfl',
        'Трансфер' => 'transfer',
        'Административные' => '5.1;5.2;5.3;5.4;5.5;5.6;5.7;5.15;5.17;7.16;7.17;7.19;7.20;7.21;7.22;7.23;7.24;7.25;7.26;7.27;7.29;7.30;7.31;7.32',
        'Питание' => '5.13;5.13.1;5.13.2',
        'Проживание' => '5.14;5.14.1;5.14.2;7.13',
        'Инстр&оргтех' => '9.1;9.2;9.3;9.4;9.5;9.7;5.16;7.28',
        'Миграционные/ Регистрация рабочих, патенты + Медобслуживание + Аттестация' => '5.10;7.12',
        'Транспорт (перевозка сотрудников) + Авиабилеты' => '5.11;5.1.11;5.11.2;5.12;7.14;7.15',
        'Спецодежда и СИЗ' => '5.8',
        'Банковские расходы (комиссии, % по кредитам)' => '7.6',
        'Налоги, в т.ч.:' => 'accrued_taxes',
        '- НДС' => 'accrued_taxes_nds',
        '- налог на прибыль' => 'accrued_taxes_receive',
        '- транспортный налог' => 'accrued_taxes_transport',
    ];

    const OBJECTS_GROUPS = [
        'ФОТ рабочие' => 'workers_salary',
        'ФОТ ИТР' => 'itr_salary',
        'Налоги с з/п' => 'ndfl',
        'Административные' => '5.1;5.2;5.3;5.4;5.5;5.6;5.7;5.15;5.17;7.20;7.21;7.25;7.26;7.30;7.31;7.32',
        'Питание' => '5.13;5.13.1;5.13.2',
        'Проживание' => '5.14;5.14.1;5.14.2',
        'Инстр&оргтех' => '9.1;9.2;9.3;9.4;9.5;9.7;5.16 ',
        'Миграционные/ Регистрация рабочих / патенты + Медобслуживание + Аттестация' => '5.10',
        'Транспорт (перевозка сотрудников) + Авиабилеты' => '5.11;5.1.11;5.11.2;5.12',
        'Спецодежда и СИЗ' => '5.8',
        'Банковские расходы (ком.БГ, обсл.спец.сч)' => '7.6',
        'Расходы офиса' => 'general_costs_percent',
        'Трансферные расходы' => 'transfer',
        'Начисленные налоги, в т.ч.:' => 'accrued_taxes',
        '- НДС' => 'accrued_taxes_nds',
        '- налог на прибыль' => 'accrued_taxes_receive',
        '- транспортный налог' => 'accrued_taxes_transport',
    ];

    public function getPivotInfoByCompany($years): array
    {
        $info = [
            'years' => [],
            'data' => [],
            'hours' => [],
            'total' => [
                'amount' => [
                    'total' => 0
                ],
                'rate' => [
                    'total' => 0
                ],
                'hours' => [
                    'total' => 0
                ],
            ]
        ];

        foreach ($years as $year) {
            $info['years'][] = [
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

        $ITRSalaryPivot = Cache::get('itr_salary_pivot_data_excel', []);
        $NDFLPivot = Cache::get('ndfl_pivot_data_excel', []);
        $pivotCodePaymentsByPeriod = Cache::get('GetBillSumByCodeReportingPeriod', []);

        foreach ($info['years'] as $year) {
            foreach ($year['quarts'] as $quart) {
                foreach ($quart['months'] as $month) {

                    if (!isset($info['hours'][$year['name']][$quart['name']]['total'])) {
                        $info['hours'][$year['name']][$quart['name']]['total'] = 0;
                    }

                    if (!isset($info['hours'][$year['name']]['total'])) {
                        $info['hours'][$year['name']]['total'] = 0;
                    }

                    $hours = Workhour::whereBetween('date', $month['period'])->sum('hours');
                    $info['hours'][$year['name']][$quart['name']][$month['name']] = $hours;
                    $info['total']['hours']['total'] += $hours;

                    $info['hours'][$year['name']][$quart['name']]['total'] += $hours;
                    $info['hours'][$year['name']]['total'] += $hours;

                    foreach (self::COMPANY_GROUPS as $group => $codes) {
                        $codes = explode(';', $codes);

                        if ($codes[0] === 'ndfl') {
                            $amount = 0;

                            $ndflAmount = ($NDFLPivot['ndfl'][$month['date_name']]['total'] ?? 0) + ($NDFLPivot['strah'][$month['date_name']]['total'] ?? 0);

                            if ($ndflAmount != 0) {
                                $amount += $ndflAmount;
                            } else {
                                $amount += (float) Payment::whereBetween('date', $month['period'])->where('amount', '<', 0)->whereIn('code', ['7.3', '7.3.1', '7.3.2'])->sum('amount');
                            }
                        } elseif ($codes[0] === 'accrued_taxes') {
                            $amount = AccruedTax::whereBetween('date', $month['period'])->sum('amount');
                            $amount += (float) Payment::whereBetween('date', $month['period'])->where('amount', '<', 0)->whereIn('code', ['7.5'])->sum('amount');
                        } elseif ($codes[0] === 'accrued_taxes_nds') {
                            $amount = AccruedTax::where('name', 'НДС')->whereBetween('date',$month['period'])->sum('amount');
                        } elseif ($codes[0] === 'accrued_taxes_receive') {
                            $amount = AccruedTax::where('name', 'Налог на прибыль')->whereBetween('date', $month['period'])->sum('amount');
                        } elseif ($codes[0] === 'accrued_taxes_transport') {
                            $amount = AccruedTax::where('name', 'Транспортный налог')->whereBetween('date', $month['period'])->sum('amount');
                        } elseif ($codes[0] === 'transfer') {
                            $amount = (float) Payment::whereBetween('date', $month['period'])
                                ->where('amount', '<', 0)
                                ->where('code', '7.11.1')
                                ->where('description', 'LIKE', '%transfer trosak%')
                                ->sum('amount');
                        } elseif ($codes[0] === 'workers_salary') {
                            $amount = (float) WorkhourPivot::where('date', $month['date_name'])->where('is_main', true)->sum('amount');
//                              $amount = 0;
                        } elseif ($codes[0] === 'itr_salary') {
                            $amount = 0;

                            foreach ($ITRSalaryPivot as $date => $pivot) {
                                if ($date >= $month['period'][0] && $date <= $month['period'][1]) {
                                    $amount += $pivot['total'];
                                }
                            }
                        } else {
                            if ($month['date_name'] >= '2025-10') {
                                $amount = 0;

                                if (isset($pivotCodePaymentsByPeriod[$month['date_name']])) {
                                    $mInfo = $pivotCodePaymentsByPeriod[$month['date_name']];
                                    foreach ($codes as $code) {
                                        $amount += $mInfo[$code] ?? 0;
                                    }
                                }
                            } else {
                                $amount = (float) Payment::whereBetween('date', $month['period'])->where('amount', '<', 0)->whereIn('code', $codes)->sum('amount');
                            }
                        }

                        $amount = -abs($amount);

                        $rate = $info['hours'][$year['name']][$quart['name']][$month['name']] != 0 ? $amount / $info['hours'][$year['name']][$quart['name']][$month['name']] : 0;

                        $info['data'][$group]['amount'][$year['name']][$quart['name']][$month['name']] = $amount;
                        $info['data'][$group]['rate'][$year['name']][$quart['name']][$month['name']] = $rate;

                        if (!isset($info['data'][$group]['amount'][$year['name']]['total'])) {
                            $info['data'][$group]['amount'][$year['name']]['total'] = 0;
                            $info['data'][$group]['rate'][$year['name']]['total'] = 0;
                        }

                        if (!isset($info['data'][$group]['amount'][$year['name']][$quart['name']]['total'])) {
                            $info['data'][$group]['amount'][$year['name']][$quart['name']]['total'] = 0;
                            $info['data'][$group]['rate'][$year['name']][$quart['name']]['total'] = 0;
                        }

                        if (!isset($info['data'][$group]['total']['amount']['total'])) {
                            $info['data'][$group]['total']['amount']['total'] = 0;
                            $info['data'][$group]['total']['rate']['total'] = 0;
                        }

                        if (! isset($info['total']['amount'][$year['name']][$quart['name']][$month['name']])) {
                            $info['total']['amount'][$year['name']][$quart['name']][$month['name']] = 0;
                        }

                        if (! isset($info['total']['rate'][$year['name']][$quart['name']][$month['name']])) {
                            $info['total']['rate'][$year['name']][$quart['name']][$month['name']] = 0;
                        }

                        if (! isset($info['total']['amount'][$year['name']][$quart['name']]['total'])) {
                            $info['total']['amount'][$year['name']][$quart['name']]['total'] = 0;
                        }

                        if (! isset($info['total']['rate'][$year['name']][$quart['name']]['total'])) {
                            $info['total']['rate'][$year['name']][$quart['name']]['total'] = 0;
                        }

                        if (! isset($info['total']['amount'][$year['name']]['total'])) {
                            $info['total']['amount'][$year['name']]['total'] = 0;
                        }

                        if (! isset($info['total']['rate'][$year['name']]['total'])) {
                            $info['total']['rate'][$year['name']]['total'] = 0;
                        }

                        $info['data'][$group]['amount'][$year['name']]['total'] += $amount;
                        $info['data'][$group]['rate'][$year['name']]['total'] += $rate;

                        $info['data'][$group]['amount'][$year['name']][$quart['name']]['total'] += $amount;
                        $info['data'][$group]['rate'][$year['name']][$quart['name']]['total']  += $rate;

                        $info['data'][$group]['total']['amount']['total'] += $amount;
                        $info['data'][$group]['total']['rate']['total'] += $rate;

                        $info['total']['amount'][$year['name']][$quart['name']][$month['name']] += $amount;
                        $info['total']['rate'][$year['name']][$quart['name']][$month['name']] += $rate;

                        $info['total']['amount'][$year['name']][$quart['name']]['total'] += $amount;
                        $info['total']['rate'][$year['name']][$quart['name']]['total'] += $rate;

                        $info['total']['amount'][$year['name']]['total'] += $amount;
                        $info['total']['rate'][$year['name']]['total'] += $rate;

                        $info['total']['amount']['total'] += $amount;

                        $info['data'][$group]['total']['rate']['total'] = $info['total']['hours']['total'] === 0 ? 0 : $info['data'][$group]['total']['amount']['total'] / $info['total']['hours']['total'];

                        $info['data'][$group]['rate'][$year['name']][$quart['name']]['total'] = $info['hours'][$year['name']][$quart['name']]['total'] === 0 ? 0 : $info['data'][$group]['amount'][$year['name']][$quart['name']]['total'] / $info['hours'][$year['name']][$quart['name']]['total'];
                        $info['data'][$group]['rate'][$year['name']]['total'] = $info['hours'][$year['name']]['total'] === 0 ? 0 : $info['data'][$group]['amount'][$year['name']]['total'] / $info['hours'][$year['name']]['total'];
                    }

                    $info['total']['rate']['total'] = $info['total']['hours']['total'] === 0 ? 0 : $info['total']['amount']['total'] / $info['total']['hours']['total'];
                    $info['total']['rate'][$year['name']]['total'] = $info['hours'][$year['name']]['total'] === 0 ? 0 : $info['total']['amount'][$year['name']]['total'] / $info['hours'][$year['name']]['total'];
                    $info['total']['rate'][$year['name']][$quart['name']]['total'] = $info['hours'][$year['name']][$quart['name']]['total'] === 0 ? 0 : $info['total']['amount'][$year['name']][$quart['name']]['total'] / $info['hours'][$year['name']][$quart['name']]['total'];
                }
            }
        }

        return $info;
    }

    public function getPivotInfoByObjects($objectIds): array
    {
        $objects = BObject::whereIn('id', $objectIds)->orderBy('code')->get();

        $contractStartDate = '';
        $contractEndDate = '';
        $contractLastStartDate = Payment::whereIn('object_id', $objectIds)->orderBy('date')->first();
        $contractLastEndDate = Payment::whereIn('object_id', $objectIds)->orderByDesc('date')->first();
//        $contractLastStartDate = Contract::whereIn('object_id', $objectIds)->where('start_date', '!=', null)->get()->sortBy('start_date', SORT_NATURAL)->first();
//        $contractLastEndDate = Contract::whereIn('object_id', $objectIds)->where('end_date', '!=', null)->get()->sortBy('end_date', SORT_NATURAL)->last();

        if ($contractLastStartDate) {
            $contractStartDate = $contractLastStartDate->date;
        }

        if ($contractLastEndDate) {
            $contractEndDate = $contractLastEndDate->date;
        }

        $infoByObjects = [
            'years' => [],
            'objects' => [],
        ];

        if (empty($contractStartDate) && empty($contractEndDate)) {
            return $infoByObjects;
        }

        $years = [];
        $startYear = Carbon::parse($contractStartDate)->year;
        $endYear = Carbon::parse($contractEndDate)->year;

        for ($i = $endYear; $i >= $startYear; $i--) {
            $years[] = $i;
        }

        foreach ($years as $year) {
            $infoByObjects['years'][] = [
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

        $object27_1 = BObject::where('code', '27.1')->first();
        $ITRSalaryPivot = Cache::get('itr_salary_pivot_data_excel', []);

        foreach ($infoByObjects['years'] as $year) {
            foreach ($year['quarts'] as $quart) {
                foreach ($quart['months'] as $month) {
                    $workhourPercents = [];

                    $objectHoursInfo = [];
                    $workhours = Workhour::whereBetween('date', $month['period'])->get();
                    foreach ($workhours->groupBy('o_id') as $oIds => $wks) {
                        $cObject = CObject::find($oIds);
                        if (!$cObject) {
                            $objectHoursInfo['none'] = $wks->sum('hours');
                        } else {
                            $objectHoursInfo[$cObject->code] = $wks->sum('hours');
                        }
                    }

                    $wObjects = [];

                    foreach ($objectHoursInfo as $oCode => $hours) {
                        $mainCode = $oCode === '27.1' ? $oCode : substr($oCode, 0, strpos($oCode, '.'));

                        if (!isset($wObjects[$mainCode])) {
                            $wObjects[$mainCode] = 0;
                        }

                        $wObjects[$mainCode] += $hours;
                    }

                    $sum = array_sum($wObjects);
                    foreach ($wObjects as $oCode => $hours) {
                        $workhourPercents[$month['name']][$oCode] = $hours / $sum;
                    }

                    foreach ($objects as $object) {
                        $crmObjects = CObject::where('code', 'LIKE', $object->code . '.%')->pluck('id')->toArray();

                        if (!isset($infoByObjects['objects'][$object->getName()])) {
                            $info = [
                                'data' => [],
                                'hours' => [],
                                'total' => [
                                    'amount' => [
                                        'total' => 0
                                    ],
                                    'rate' => [
                                        'total' => 0
                                    ],
                                    'hours' => [
                                        'total' => 0
                                    ],
                                ]
                            ];
                        } else {
                            $info = $infoByObjects['objects'][$object->getName()];
                        }

                        if (!isset($info['hours'][$year['name']][$quart['name']]['total'])) {
                            $info['hours'][$year['name']][$quart['name']]['total'] = 0;
                        }

                        if (!isset($info['hours'][$year['name']]['total'])) {
                            $info['hours'][$year['name']]['total'] = 0;
                        }

                        $hours = Workhour::whereBetween('date', $month['period'])->whereIn('o_id', $crmObjects)->sum('hours');
                        $info['hours'][$year['name']][$quart['name']][$month['name']] = $hours;
                        $info['total']['hours']['total'] += $hours;

                        $info['hours'][$year['name']][$quart['name']]['total'] += $hours;
                        $info['hours'][$year['name']]['total'] += $hours;

                        foreach (self::OBJECTS_GROUPS as $group => $codes) {
                            $codes = explode(';', $codes);

                            if ($codes[0] === 'ndfl') {
                                $amount = 0;

                                $ndflAmount = ($NDFLPivot['ndfl'][$month['date_name']]['objects'][$object->code] ?? 0) + ($NDFLPivot['strah'][$month['date_name']]['objects'][$object->code] ?? 0);

                                if ($ndflAmount != 0) {
                                    $amount += $ndflAmount;
                                } else {
                                    $amount += $amount = (float) Payment::whereBetween('date', $month['period'])
                                        ->where('amount', '<', 0)
                                        ->whereIn('code', ['7.3', '7.3.1', '7.3.2'])
                                        ->where('object_id', $object->id)
                                        ->sum('amount');
                                }
                            } elseif ($codes[0] === 'general_costs_percent') {
                                $paymentQuery = \App\Models\Payment::query()->whereBetween('date', $month['period'])->whereIn('company_id', [1, 5]);
                                $generalAmount = (clone $paymentQuery)->whereNotIn('code', ['7.1', '7.2', '7.5', '7.11', '7.11.1', '7.11.2'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount')
                                    + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount');
                                $amount = $generalAmount * ($workhourPercents[$month['name']][$object->code] ?? 0);
                            } elseif ($codes[0] === 'transfer') {
                                $transferData = ObjectService::getDistributionTransferServiceByPeriod($month['period']);
                                $amount = -abs($transferData[$object->id]['transfer_amount'] ?? 0);
                            } elseif ($codes[0] === 'accrued_taxes') {
                                $amount = -1 * abs(AccruedTax::whereBetween('date', $month['period'])->sum('amount') * ($workhourPercents[$month['name']][$object->code] ?? 0));
                            } elseif ($codes[0] === 'accrued_taxes_nds') {
                                $amount = -1 * abs(AccruedTax::where('name', 'НДС')->whereBetween('date', $month['period'])->sum('amount') * ($workhourPercents[$month['name']][$object->code] ?? 0));
                            } elseif ($codes[0] === 'accrued_taxes_receive') {
                                $amount = -1 * abs(AccruedTax::where('name', 'Налог на прибыль')->whereBetween('date', $month['period'])->sum('amount') * ($workhourPercents[$month['name']][$object->code] ?? 0));
                            } elseif ($codes[0] === 'accrued_taxes_transport') {
                                $amount = -1 * abs(AccruedTax::where('name', 'Транспортный налог')->whereBetween('date', $month['period'])->sum('amount') * ($workhourPercents[$month['name']][$object->code] ?? 0));
                            } elseif ($codes[0] === 'workers_salary') {
//                                $amount = 0;
                                $amount = (float) WorkhourPivot::where('date', $month['date_name'])->where('is_main', true)->where('code', $object->code)->sum('amount');
                            } elseif ($codes[0] === 'itr_salary') {
                                $amount = 0;

                                foreach ($ITRSalaryPivot as $date => $pivot) {
                                    if ($date >= $month['period'][0] && $date <= $month['period'][1]) {
                                        foreach ($pivot['objects'] as $code => $am) {
                                            if ($code == $object->code) {
                                                $amount += $am;
                                            }
                                        }
                                    }
                                }
                            } else {

                                if ($month['date_name'] >= '2025-10') {
                                    $amount = 0;

                                    if (isset($pivotCodePaymentsByPeriod[$month['date_name']][$object->code])) {
                                        $mInfo = $pivotCodePaymentsByPeriod[$month['date_name']][$object->code];
                                        foreach ($codes as $code) {
                                            $amount += $mInfo[$code] ?? 0;
                                        }
                                    }
                                } else {
                                    $amount = (float)Payment::whereBetween('date', $month['period'])
                                        ->where('amount', '<', 0)
                                        ->whereIn('code', $codes)
                                        ->where('object_id', $object->id)
                                        ->sum('amount');
                                }
                            }

                            $amount = -abs($amount);

                            $rate = $info['hours'][$year['name']][$quart['name']][$month['name']] != 0 ? $amount / $info['hours'][$year['name']][$quart['name']][$month['name']] : 0;

                            $info['data'][$group]['amount'][$year['name']][$quart['name']][$month['name']] = $amount;
                            $info['data'][$group]['rate'][$year['name']][$quart['name']][$month['name']] = $rate;

                            if (!isset($info['data'][$group]['amount'][$year['name']]['total'])) {
                                $info['data'][$group]['amount'][$year['name']]['total'] = 0;
                                $info['data'][$group]['rate'][$year['name']]['total'] = 0;
                            }

                            if (!isset($info['data'][$group]['amount'][$year['name']][$quart['name']]['total'])) {
                                $info['data'][$group]['amount'][$year['name']][$quart['name']]['total'] = 0;
                                $info['data'][$group]['rate'][$year['name']][$quart['name']]['total'] = 0;
                            }

                            if (!isset($info['data'][$group]['total']['amount']['total'])) {
                                $info['data'][$group]['total']['amount']['total'] = 0;
                                $info['data'][$group]['total']['rate']['total'] = 0;
                            }

                            if (! isset($info['total']['amount'][$year['name']][$quart['name']][$month['name']])) {
                                $info['total']['amount'][$year['name']][$quart['name']][$month['name']] = 0;
                            }

                            if (! isset($info['total']['rate'][$year['name']][$quart['name']][$month['name']])) {
                                $info['total']['rate'][$year['name']][$quart['name']][$month['name']] = 0;
                            }

                            if (! isset($info['total']['amount'][$year['name']][$quart['name']]['total'])) {
                                $info['total']['amount'][$year['name']][$quart['name']]['total'] = 0;
                            }

                            if (! isset($info['total']['rate'][$year['name']][$quart['name']]['total'])) {
                                $info['total']['rate'][$year['name']][$quart['name']]['total'] = 0;
                            }

                            if (! isset($info['total']['amount'][$year['name']]['total'])) {
                                $info['total']['amount'][$year['name']]['total'] = 0;
                            }

                            if (! isset($info['total']['rate'][$year['name']]['total'])) {
                                $info['total']['rate'][$year['name']]['total'] = 0;
                            }

                            $info['data'][$group]['amount'][$year['name']]['total'] += $amount;
                            $info['data'][$group]['rate'][$year['name']]['total'] += $rate;

                            $info['data'][$group]['amount'][$year['name']][$quart['name']]['total'] += $amount;
                            $info['data'][$group]['rate'][$year['name']][$quart['name']]['total']  += $rate;

                            $info['data'][$group]['total']['amount']['total'] += $amount;
                            $info['data'][$group]['total']['rate']['total'] += $rate;

                            $info['total']['amount'][$year['name']][$quart['name']][$month['name']] += $amount;
                            $info['total']['rate'][$year['name']][$quart['name']][$month['name']] += $rate;

                            $info['total']['amount'][$year['name']][$quart['name']]['total'] += $amount;
                            $info['total']['rate'][$year['name']][$quart['name']]['total'] += $rate;

                            $info['total']['amount'][$year['name']]['total'] += $amount;
                            $info['total']['rate'][$year['name']]['total'] += $rate;

                            $info['total']['amount']['total'] += $amount;

                            $info['data'][$group]['total']['rate']['total'] = $info['total']['hours']['total'] === 0 ? 0 : $info['data'][$group]['total']['amount']['total'] / $info['total']['hours']['total'];
                        }

                        $info['total']['rate']['total'] = $info['total']['hours']['total'] === 0 ? 0 : $info['total']['amount']['total'] / $info['total']['hours']['total'];
                        $info['total']['rate'][$year['name']]['total'] = $info['hours'][$year['name']]['total'] === 0 ? 0 : $info['total']['amount'][$year['name']]['total'] / $info['hours'][$year['name']]['total'];
                        $info['total']['rate'][$year['name']][$quart['name']]['total'] = $info['hours'][$year['name']][$quart['name']]['total'] === 0 ? 0 : $info['total']['amount'][$year['name']][$quart['name']]['total'] / $info['hours'][$year['name']][$quart['name']]['total'];

                        $infoByObjects['objects'][$object->getName()] = $info;
                    }
                }
            }
        }
        return $infoByObjects;
    }
}