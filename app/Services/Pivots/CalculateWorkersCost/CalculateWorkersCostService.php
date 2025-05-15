<?php

namespace App\Services\Pivots\CalculateWorkersCost;

use App\Models\CRM\Workhour;
use App\Models\Payment;

class CalculateWorkersCostService
{
    const COMPANY_GROUPS = [
        'ФОТ офис + ФОТ ИТР' => '7.8.1;7.9.1',
        'ФОТ рабочие' => '7.8;7.8.2;7.9.2;7.9',
        'НДФЛ + страховые взносы + трансфер' => '7.3;7.3.1;7.3.2;7.11',
        'Административные' => '5.1;5.2;5.3;5.4;5.5;5.6;5.7;5.15;5.17;7.16;7.17;7.19;7.20;7.21;7.22;7.23;7.24;7.25;7.26;7.27;7.29;7.30;7.31;7.32',
        'Питание' => '5.13/5.13.1/5.13.2',
        'Проживание' => '5.14;5.14.1;5.14.2;7.13',
        'Инстр&оргтех' => '9.1;9.2;9.3;9.4;9.5;9.7;5.16;7.28',
        'Миграционные/ Регистрация рабочих, патенты + Медобслуживание + Аттестация' => '5.10;7.12',
        'Транспорт (перевозка сотрудников) + Авиабилеты' => '5.11;5.1.11;5.11.2;5.12;7.14;7.15',
        'Спецодежда и СИЗ' => '5.8',
        'Банковские расходы (комиссии, % по кредитам)' => '7.6;7.7.1',
        'Налоги, в т.ч.:' => '',
        '- НДС' => '7.1',
        '- налог на прибыль' => '7.2',
        '- налог на имущество' => '7.5',
        '- транспортный налог' => '7.5',
    ];

    public function getPivotInfo(): array
    {
        $year = 2024;
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
                $year => []
            ],
            'total' => [
                'amount' => [$year => []],
                'rate' => [$year => []],
            ]
        ];

        foreach ($quarts as $index => $quart) {
            $info['rates'][$year]['quarts'][$index] = Workhour::whereBetween('date', [$quart[0], $quart[1]])->sum('hours');
        }

        foreach (self::COMPANY_GROUPS as $group => $codes) {
            $codes = explode(';', $codes);

            $item = [
                'group' => $group,
                'quarts' => []
            ];

            foreach ($quarts as $index => $quart) {
                $amount = (float) Payment::whereBetween('date', [$quart[0], $quart[1]])->where('amount', '<', 0)->whereIn('code', $codes)->sum('amount');
                $rate = $amount / $info['rates'][$year]['quarts'][$index];

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
            }

            $info['data'][$year][] = $item;
        }

        return $info;
    }
}