<?php

namespace App\Services\Object\Report;

use App\Models\AccruedTax\AccruedTax;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\SERVICE\WorkhourPivot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PaymentReceiveReportService
{
    public function getReportInfo(BObject $object): array
    {
        $contractStartDate = '';
        $contractEndDate = '';
        $contractLastStartDate = $object->payments()->orderBy('date')->first();
        $contractLastEndDate = $object->payments()->orderByDesc('date')->first();

        if ($contractLastStartDate) {
            $contractStartDate = $contractLastStartDate->date;
        }

        if ($contractLastEndDate) {
            $contractEndDate = $contractLastEndDate->date;
        }

        $info = [
            'years' => [],
            'receive' => [],
            'payment' => [],
        ];

        if (empty($contractStartDate) && empty($contractEndDate)) {
            return $info;
        }

        $years = [];
        $startYear = Carbon::parse($contractStartDate)->year;
        $endYear = Carbon::parse($contractEndDate)->year;

        for ($i = $endYear; $i >= $startYear; $i--) {
            $years[] = $i;
        }

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

        $objectActs = $object->acts;
        $ITRSalaryPivot = Cache::get('itr_salary_pivot_data_excel', []);
        $paymentPeriodData = Cache::get('p_and_l_payments_period_1c_data', []);
        $transferCacheData = Cache::get('calc_workers_cost_transfer_data', []);

        $object27_1 = BObject::where('code', '27.1')->first();
        $financeReportHistory = FinanceReportHistory::getCurrentFinanceReportForObject($object);

        foreach ($info['years'] as $year) {
            foreach ($year['quarts'] as $quart) {
                foreach ($quart['months'] as $month) {
                    // receive

                    $material = $objectActs->whereBetween('date', $month['period'])->sum('amount');
                    $rad = $objectActs->whereBetween('date', $month['period'])->sum('rad_amount');
                    $service = $objectActs->whereBetween('date', $month['period'])->sum('opste_amount');

                    $this->fillInfo($info['receive']['material'], $year['name'], $quart['name'], $month['name'], $material);
                    $this->fillInfo($info['receive']['rad'], $year['name'], $quart['name'], $month['name'], $rad);
                    $this->fillInfo($info['receive']['service'], $year['name'], $quart['name'], $month['name'], $service);

                    $this->fillInfo($info['receive'], $year['name'], $quart['name'], $month['name'], $material + $rad + $service);

                    // payment

                    $receivePercents = [];
                    $objectsReceives = [];
                    $receives = Payment::whereBetween('date', $month['period'])
                        ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                        ->where('amount', '>', 0)
                        ->get();

                    foreach ($receives->groupBy('object_id') as $oId => $payments) {
                        $objectsReceives[$oId] = $payments->sum('amount');
                    }

                    $sum = array_sum($objectsReceives);
                    foreach ($objectsReceives as $oId => $amount) {
                        $receivePercents[$month['date_name']][$oId] = $amount / $sum;
                    }

                    $itrAmount = 0;
                    foreach ($ITRSalaryPivot as $date => $pivot) {
                        if ($date >= $month['period'][0] && $date <= $month['period'][1]) {
                            foreach ($pivot['objects'] as $code => $am) {
                                if ($code == $object->code) {
                                    $itrAmount += $am;
                                }
                            }
                        }
                    }

                    $paymentQuery = Payment::query()->whereBetween('date', $month['period'])->whereIn('company_id', [1, 5]);
                    $generalAmount = (clone $paymentQuery)->whereNotIn('code', ['7.1', '7.2', '7.5', '7.11'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount')
                        + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount');

                    $contractorsMaterial = 0;
                    $contractorsRad = 0;
                    $providersMaterial = Payment::whereBetween('date', $month['period'])->where('amount', '<', 0)->where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->sum('amount');
                    $serviceService = 0;

                    foreach ($paymentPeriodData as $paymentPeriodInfo) {
                        if ($paymentPeriodInfo['Period'] !== ($month['date_name'])) {
                            continue;
                        }

                        foreach ($paymentPeriodInfo['Objects'] as $oInfo) {
                            if ($oInfo['Object'] !== $object->code) {
                                continue;
                            }

                            foreach ($oInfo['ContractorGroups'] as $groupInfo) {
                                if ($groupInfo['ContractorGroup'] === 'РАБОТЫ') {
                                    $contractorsRad = -$groupInfo['Details'][0]['Sum'];
                                }

                                if ($groupInfo['ContractorGroup'] === 'НАКЛАДНЫЕ/УСЛУГИ') {
                                    $serviceService = -$groupInfo['Details'][0]['Sum'];
                                }
                            }
                        }
                    }

                        $salaryWorkers = -abs((float) WorkhourPivot::where('date', $month['date_name'])
                            ->where('is_main', true)
                            ->where('code', $object->code)
                            ->sum('amount'));

                        $salaryItr = -abs($itrAmount);
                        $salaryTaxes = -abs(0);
                        $transfer = -abs($transferCacheData[$month['date_name']][$object->id]['transfer_amount'] ?? 0);
                        $generalCosts = -abs($generalAmount * ($financeReportHistory['general_balance_to_receive_percentage'] ?? 0) / 100);
                        $accruedTaxes = -abs(AccruedTax::whereBetween('date', $month['period'])->sum('amount') * ($receivePercents[$month['date_name']][$object->id] ?? 0));

                        $this->fillInfo($info['payment']['contractors']['material'], $year['name'], $quart['name'], $month['name'], $contractorsMaterial);
                        $this->fillInfo($info['payment']['contractors']['rad'], $year['name'], $quart['name'], $month['name'], $contractorsRad);
                        $this->fillInfo($info['payment']['providers']['material'], $year['name'], $quart['name'], $month['name'], $providersMaterial);
                        $this->fillInfo($info['payment']['service']['service'], $year['name'], $quart['name'], $month['name'], $serviceService);

                        $this->fillInfo($info['payment']['salary_workers'], $year['name'], $quart['name'], $month['name'], $salaryWorkers);
                        $this->fillInfo($info['payment']['salary_itr'], $year['name'], $quart['name'], $month['name'], $salaryItr);
                        $this->fillInfo($info['payment']['salary_taxes'], $year['name'], $quart['name'], $month['name'], $salaryTaxes);
                        $this->fillInfo($info['payment']['transfer'], $year['name'], $quart['name'], $month['name'], $transfer);
                        $this->fillInfo($info['payment']['general_costs'], $year['name'], $quart['name'], $month['name'], $generalCosts);
                        $this->fillInfo($info['payment']['accrued_taxes'], $year['name'], $quart['name'], $month['name'], $accruedTaxes);

                        $this->fillInfo(
                            $info['payment'],
                            $year['name'],
                            $quart['name'],
                            $month['name'],
                            $contractorsMaterial + $contractorsRad + $providersMaterial + $serviceService + $salaryWorkers + $salaryItr + $salaryTaxes + $transfer + $generalCosts + $accruedTaxes
                        );
                    }
                }
            }

        return $info;
    }

    public function fillInfo(&$info, $year, $quart, $month, $value): void
    {
        $info[$year][$quart][$month] = $value;

        if (!isset($info[$year][$quart]['total'])) {
            $info[$year][$quart]['total'] = 0;
        }
        $info[$year][$quart]['total'] += $value;

        if (!isset( $info[$year]['total'])) {
            $info[$year]['total'] = 0;
        }
        $info[$year]['total'] += $value;

        if (!isset( $info['total'])) {
            $info['total'] = 0;
        }
        $info['total'] += $value;
    }
}
