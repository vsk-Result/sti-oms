<?php

namespace App\Services\Pivots\CalculateWorkersCost;

use App\Models\AccruedTax\AccruedTax;
use App\Models\CRM\CObject;
use App\Models\CRM\Employee;
use App\Models\CRM\Workhour;
use App\Models\Object\BObject;
use App\Models\Payment;

class CalculateWorkersCostService
{
    const COMPANY_GROUPS = [
        'ФОТ офис + ФОТ ИТР' => '7.8.1;7.9.1',
        'ФОТ рабочие' => '7.8;7.8.2;7.9.2;7.9',
        'НДФЛ + страховые взносы + трансфер' => '7.3;7.3.1;7.3.2;7.11.1',
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
        '- налог на имущество' => '7.5',
        '- транспортный налог' => 'accrued_taxes_transport',
    ];

    const OBJECTS_GROUPS = [
        'ФОТ ИТР' => '7.8.1;7.9.1',
        'ФОТ рабочие' => '7.8;7.8.2;7.9.2;7.9',
        'Налоги с з/п' => '7.3;7.3.1;7.3.2',
        'Административные' => '5.1;5.2;5.3;5.4;5.5;5.6;5.7;5.15;5.17;7.20;7.21;7.25;7.26;7.30;7.31;7.32',
        'Питание' => '5.13;5.13.1;5.13.2',
        'Проживание' => '5.14;5.14.1;5.14.2',
        'Инстр&оргтех' => '9.1;9.2;9.3;9.4;9.5;9.7;5.16 ',
        'Миграционные/ Регистрация рабочих / патенты + Медобслуживание + Аттестация' => '5.10',
        'Транспорт (перевозка сотрудников) + Авиабилеты' => '5.11;5.1.11;5.11.2;5.12',
        'Спецодежда и СИЗ' => '5.8',
        'Банковские расходы (ком.БГ, обсл.спец.сч)' => '7.6',
        'Расходы офиса : 27,1+общ.расходы (ус.трансфера+банк.комис)' => 'general_costs_percent',
        'Начисленные налоги, в т.ч.:' => 'accrued_taxes',
        '- НДС' => 'accrued_taxes_nds',
        '- налог на прибыль' => 'accrued_taxes_receive',
        '- транспортный налог' => 'accrued_taxes_transport',
    ];

    public function getPivotInfoByCompany($year): array
    {
        $quarts = [
            '1 квартал' => [$year . '-01-01', $year . '-03-31'],
            '2 квартал' => [$year . '-04-01', $year . '-06-30'],
            '3 квартал' => [$year . '-07-01', $year . '-09-30'],
            '4 квартал' => [$year . '-10-01', $year . '-12-31'],
        ];

        $info = [
            'years' => [
                $year => $quarts
            ],
            'data' => [
                $year => []
            ],
            'rates' => [
                $year => [],
            ],
            'total' => [
                'amount' => [$year => []],
                'rate' => [$year => []],
                'total' => [
                    'amount' => 0,
                    'rate' => 0,
                    'hours' => 0,
                ]
            ]
        ];

        foreach ($quarts as $index => $quart) {
            $hours = Workhour::whereBetween('date', [$quart[0], $quart[1]])->sum('hours');

            $info['rates'][$year]['quarts'][$index] = $hours;
            $info['total']['total']['hours'] += $hours;
        }

        foreach (self::COMPANY_GROUPS as $group => $codes) {
            $codes = explode(';', $codes);

            $item = [
                'group' => $group,
                'quarts' => [],
                'total' => [
                    'amount' => 0,
                    'rate' => 0,
                ],
            ];

            foreach ($quarts as $index => $quart) {
                if ($codes[0] === 'accrued_taxes') {
                    $amount = AccruedTax::whereBetween('date', [$quart[0], $quart[1]])->sum('amount');
                    $amount += (float) Payment::whereBetween('date', [$quart[0], $quart[1]])->where('amount', '<', 0)->whereIn('code', ['7.5'])->sum('amount');
                } elseif ($codes[0] === 'accrued_taxes_nds') {
                    $amount = AccruedTax::where('name', 'НДС')->whereBetween('date', [$quart[0], $quart[1]])->sum('amount');
                } elseif ($codes[0] === 'accrued_taxes_receive') {
                    $amount = AccruedTax::where('name', 'Налог на прибыль')->whereBetween('date', [$quart[0], $quart[1]])->sum('amount');
                } elseif ($codes[0] === 'accrued_taxes_transport') {
                    $amount = AccruedTax::where('name', 'Транспортный налог')->whereBetween('date', [$quart[0], $quart[1]])->sum('amount');
                } elseif (in_array('7.11.1', $codes)) {
                    $amount = (float) Payment::whereBetween('date', [$quart[0], $quart[1]])
                        ->where('amount', '<', 0)
                        ->whereIn('code', ['7.3, 7.3.1, 7.3.2'])
                        ->sum('amount');
                    $amount += (float) Payment::whereBetween('date', [$quart[0], $quart[1]])
                        ->where('amount', '<', 0)
                        ->where('description', 'LIKE', '%transfer trosak%')
                        ->sum('amount');
                } elseif ($codes[0] === '7.8;7.8.2;7.9.2;7.9') {
                    $amount = (float) Payment::whereBetween('date', [$quart[0], $quart[1]])->where('amount', '<', 0)->whereIn('code', $codes)->sum('amount');

                    $payments = -abs(Payment::whereBetween('date', [$quart[0], $quart[1]])->sum('value'));
                    $amount += $payments;
                } else {
                    $amount = (float) Payment::whereBetween('date', [$quart[0], $quart[1]])->where('amount', '<', 0)->whereIn('code', $codes)->sum('amount');
                }

                $amount = -abs($amount);

                $rate = $info['rates'][$year]['quarts'][$index] != 0 ? $amount / $info['rates'][$year]['quarts'][$index] : 0;

                if (! is_valid_amount_in_range($rate)) {
                    $rate = 0;
                }

                $item['quarts'][$index] = [
                    'amount' => $amount,
                    'rate' => $rate
                ];

                if (! isset($info['total']['amount'][$year]['quarts'][$index])) {
                    $info['total']['amount'][$year]['quarts'][$index] = 0;
                }

                if (! isset($info['total']['rate'][$year]['quarts'][$index])) {
                    $info['total']['rate'][$year]['quarts'][$index] = 0;
                }

                $info['total']['amount'][$year]['quarts'][$index] += $amount;
                $info['total']['rate'][$year]['quarts'][$index] += $rate;

                $item['total']['amount'] += $amount;

                $info['total']['total']['amount'] += $amount;
            }

            $item['total']['rate'] = $item['total']['amount'] / $info['total']['total']['hours'];
            $info['total']['total']['rate'] += $item['total']['rate'];

            $info['data'][$year][] = $item;
        }

        return $info;
    }

    public function getPivotInfoByObjects($year, $objectIds): array
    {
        $quarts = [
            '1 квартал' => [$year . '-01-01', $year . '-03-31'],
            '2 квартал' => [$year . '-04-01', $year . '-06-30'],
            '3 квартал' => [$year . '-07-01', $year . '-09-30'],
            '4 квартал' => [$year . '-10-01', $year . '-12-31'],
        ];

        $infoByObjects = [
            'years' => [
                $year => $quarts
            ],
            'objects' => []
        ];
        $objects = BObject::whereIn('id', $objectIds)->orderBy('code')->get();

        $quartsWorkhoursPercents = [];

        foreach ($quarts as $index => $quart) {
            $objectIdsOnQuarts = [];
            $workhours = Workhour::whereBetween('date', [$quart[0], $quart[1]])->get();
            foreach ($workhours->groupBy('o_id') as $oIds => $wks) {
                $cObject = CObject::find($oIds);
                if (!$cObject) {
                    $objectIdsOnQuarts['none'] = $wks->sum('hours');
                } else {
                    $objectIdsOnQuarts[$cObject->code] = $wks->sum('hours');
                }
            }

            $wObjects = [];

            foreach ($objectIdsOnQuarts as $oCode => $hours) {
                $mainCode = $oCode === '27.1' ? $oCode : substr($oCode, 0, strpos($oCode, '.'));

                if (!isset($wObjects[$mainCode])) {
                    $wObjects[$mainCode] = 0;
                }

                $wObjects[$mainCode] += $hours;
            }

            $sum = array_sum($wObjects);
            foreach ($wObjects as $oCode => $hours) {
                $quartsWorkhoursPercents[$index][$oCode] = $hours / $sum;
            }
        }

        foreach ($objects as $object) {
            $crmObjects = CObject::where('code', 'LIKE', $object->code . '.%')->pluck('id')->toArray();

            $info = [
                'years' => [
                    $year => $quarts
                ],
                'data' => [
                    $year => []
                ],
                'rates' => [
                    $year => []
                ],
                'total' => [
                    'amount' => [$year => []],
                    'rate' => [$year => []],
                    'total' => [
                        'amount' => 0,
                        'rate' => 0,
                        'hours' => 0,
                    ]
                ]
            ];

            foreach ($quarts as $index => $quart) {
                $hours = Workhour::whereBetween('date', [$quart[0], $quart[1]])->whereIn('o_id', $crmObjects)->sum('hours');
                $info['rates'][$year]['quarts'][$index] = $hours;
                $info['total']['total']['hours'] += $hours;
            }

            $object27_1 = BObject::where('code', '27.1')->first();

            foreach (self::OBJECTS_GROUPS as $group => $codes) {
                $codes = explode(';', $codes);

                $item = [
                    'group' => $group,
                    'quarts' => [],
                    'total' => [
                        'amount' => 0,
                        'rate' => 0,
                    ],
                ];

                foreach ($quarts as $index => $quart) {
                    if ($codes[0] === 'general_costs_percent') {
                        $paymentQuery = \App\Models\Payment::query()->whereBetween('date', [$quart[0], $quart[1]])->whereIn('company_id', [1, 5]);
                        $generalAmount = (clone $paymentQuery)->whereNotIn('code', ['7.1', '7.2', '7.5'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount')
                            + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount');
                        $amount = $generalAmount * ($quartsWorkhoursPercents[$index][$object->code] ?? 0);
                    } elseif ($codes[0] === 'accrued_taxes') {
                        $amount = AccruedTax::whereBetween('date', [$quart[0], $quart[1]])->sum('amount') * ($quartsWorkhoursPercents[$index][$object->code] ?? 0);
                    } elseif ($codes[0] === 'accrued_taxes_nds') {
                        $amount = AccruedTax::where('name', 'НДС')->whereBetween('date', [$quart[0], $quart[1]])->sum('amount') * ($quartsWorkhoursPercents[$index][$object->code] ?? 0);
                    } elseif ($codes[0] === 'accrued_taxes_receive') {
                        $amount = AccruedTax::where('name', 'Налог на прибыль')->whereBetween('date', [$quart[0], $quart[1]])->sum('amount') * ($quartsWorkhoursPercents[$index][$object->code] ?? 0);
                    } elseif ($codes[0] === 'accrued_taxes_transport') {
                        $amount = AccruedTax::where('name', 'Транспортный налог')->whereBetween('date', [$quart[0], $quart[1]])->sum('amount') * ($quartsWorkhoursPercents[$index][$object->code] ?? 0);
                    } elseif ($codes[0] === '7.8;7.8.2;7.9.2;7.9') {
                        $amount = (float) Payment::whereBetween('date', [$quart[0], $quart[1]])
                            ->where('amount', '<', 0)
                            ->whereIn('code', $codes)
                            ->where('object_id', $object->id)
                            ->sum('amount');

                        $payments = -abs(Payment::whereBetween('date', [$quart[0], $quart[1]])->whereIn('code', $crmObjects)->sum('value'));
                        $amount += $payments;
                    } else {
                        $amount = (float) Payment::whereBetween('date', [$quart[0], $quart[1]])
                            ->where('amount', '<', 0)
                            ->whereIn('code', $codes)
                            ->where('object_id', $object->id)
                            ->sum('amount');
                    }

                    $amount = -abs($amount);

                    $rate = $info['rates'][$year]['quarts'][$index] != 0 ? $amount / $info['rates'][$year]['quarts'][$index] : 0;

                    $item['quarts'][$index] = [
                        'amount' => $amount,
                        'rate' => $rate
                    ];

                    if (! isset($info['total']['amount'][$year]['quarts'][$index])) {
                        $info['total']['amount'][$year]['quarts'][$index] = 0;
                    }

                    if (! isset($info['total']['rate'][$year]['quarts'][$index])) {
                        $info['total']['rate'][$year]['quarts'][$index] = 0;
                    }

                    $info['total']['amount'][$year]['quarts'][$index] += $amount;
                    $info['total']['rate'][$year]['quarts'][$index] += $rate;

                    $item['total']['amount'] += $amount;

                    $info['total']['total']['amount'] += $amount;
                }

                $item['total']['rate'] = $item['total']['amount'] / $info['total']['total']['hours'];
                $info['total']['total']['rate'] += $item['total']['rate'];

                $info['data'][$year][] = $item;
            }

            $infoByObjects['objects'][$object->getName()] = $info;
        }


        return $infoByObjects;
    }
}