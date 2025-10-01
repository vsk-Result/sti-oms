<?php

namespace App\Services\Object\Report;

use App\Models\AccruedTax\AccruedTax;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\SERVICE\WorkhourPivot;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PaymentReceiveReportService
{
    public function getReportInfo(BObject $object, array $requestData): array
    {
        $year = $requestData['year'];
        $monthsFull = [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь',
        ];
        $months = array_keys($monthsFull);

        $receiveInfo = [
            'material' => [$year => ['total' => 0]],
            'rad' => [$year => ['total' => 0]],
            'service' => [$year => ['total' => 0]],
            'total' => [$year => ['total' => 0]]
        ];

        foreach ($months as $month) {
            $end = Carbon::parse($year . '-' . $month . '-01')->endOfMonth()->format('Y-m-d');
            $period = [$year . '-' . $month . '-01', $end];

            $material = $object->acts()->whereBetween('date', $period)->sum('amount');
            $rad = $object->acts()->whereBetween('date', $period)->sum('rad_amount');
            $service = $object->acts()->whereBetween('date', $period)->sum('opste_amount');

            $receiveInfo['material'][$year][$month] = $material;
            $receiveInfo['rad'][$year][$month] = $rad;
            $receiveInfo['service'][$year][$month] = $service;

            $receiveInfo['material'][$year]['total'] += $material;
            $receiveInfo['rad'][$year]['total'] += $rad;
            $receiveInfo['service'][$year]['total'] += $service;

            $receiveInfo['total'][$year][$month] = $material + $rad + $service;
            $receiveInfo['total'][$year]['total'] += $receiveInfo['total'][$year][$month];
        }

        $paymentInfo = [
            'contractors' => [
                'material' => [$year => ['total' => 0]],
                'rad' => [$year => ['total' => 0]],
            ],
            'providers' => [
                'material' => [$year => ['total' => 0]],
            ],
            'service' => [
                'service' => [$year => ['total' => 0]],
            ],
            'salary_workers' => [$year => ['total' => 0]],
            'salary_itr' => [$year => ['total' => 0]],
            'salary_taxes' => [$year => ['total' => 0]],
            'transfer' => [$year => ['total' => 0]],
            'general_costs' => [$year => ['total' => 0]],
            'accrued_taxes' => [$year => ['total' => 0]],
            'total' => [$year => ['total' => 0]]
        ];

        $ITRSalaryPivot = Cache::get('itr_salary_pivot_data_excel', []);
        $paymentPeriodData = Cache::get('p_and_l_payments_period_1c_data', []);
        $object27_1 = BObject::where('code', '27.1')->first();
        $financeReportHistory = FinanceReportHistory::getCurrentFinanceReportForObject($object);

        foreach ($months as $month) {
            $end = Carbon::parse($year . '-' . $month . '-01')->endOfMonth()->format('Y-m-d');
            $period = [$year . '-' . $month . '-01', $end];

            $receivePercents = [];
            $objectsReceives = [];
            $receives = Payment::whereBetween('date', $period)
                ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                ->where('amount', '>', 0)
                ->get();

            foreach ($receives->groupBy('object_id') as $oId => $payments) {
                $objectsReceives[$oId] = $payments->sum('amount');
            }

            $sum = array_sum($objectsReceives);
            foreach ($objectsReceives as $oId => $amount) {
                $receivePercents[$year . '-' .$month][$oId] = $amount / $sum;
            }

            $itrAmount = 0;
            foreach ($ITRSalaryPivot as $date => $pivot) {
                if ($date >= $period[0] && $date <= $period[1]) {
                    foreach ($pivot['objects'] as $code => $am) {
                        if ($code == $object->code) {
                            $itrAmount += $am;
                        }
                    }
                }
            }

            $paymentQuery = Payment::query()->whereBetween('date', $period)->whereIn('company_id', [1, 5]);
            $generalAmount = (clone $paymentQuery)->whereNotIn('code', ['7.1', '7.2', '7.5', '7.11', '7.11.1', '7.11.2'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount')
                + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount');

            $transferData = ObjectService::getDistributionTransferServiceByPeriod($period);

            $contractorsMaterial = 0;
            $contractorsRad = 0;
            $providersMaterial = 0;
            $serviceService = 0;

            foreach ($paymentPeriodData as $info) {
                if ($info['Period'] !== ($year . '-' . $month)) {
                    continue;
                }

                foreach ($info['Objects'] as $oInfo) {
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

            $salaryWorkers = -abs((float) WorkhourPivot::where('date', $year . '-' . $month)
                ->where('is_main', true)
                ->where('code', $object->code)
                ->sum('amount'));

            $salaryItr = -abs($itrAmount);
            $salaryTaxes = -abs(0);
            $transfer = -abs($transferData[$object->id]['transfer_amount'] ?? 0);
            $generalCosts = -abs($generalAmount * ($financeReportHistory['general_balance_to_receive_percentage'] ?? 0) / 100);
            $accruedTaxes = -abs(AccruedTax::whereBetween('date', $period)->sum('amount') * ($receivePercents[$year . '-' . $month][$object->id] ?? 0));

            $paymentInfo['contractors']['material'][$year][$month] = $contractorsMaterial;
            $paymentInfo['contractors']['rad'][$year][$month] = $contractorsRad;
            $paymentInfo['providers']['material'][$year][$month] = $providersMaterial;
            $paymentInfo['service']['service'][$year][$month] = $serviceService;

            $paymentInfo['salary_workers'][$year][$month] = $salaryWorkers;
            $paymentInfo['salary_itr'][$year][$month] = $salaryItr;
            $paymentInfo['salary_taxes'][$year][$month] = $salaryTaxes;
            $paymentInfo['transfer'][$year][$month] = $transfer;
            $paymentInfo['general_costs'][$year][$month] = $generalCosts;
            $paymentInfo['accrued_taxes'][$year][$month] = $accruedTaxes;

            $paymentInfo['contractors']['material'][$year]['total'] += $paymentInfo['contractors']['material'][$year][$month];
            $paymentInfo['contractors']['rad'][$year]['total'] += $paymentInfo['contractors']['rad'][$year][$month];
            $paymentInfo['providers']['material'][$year]['total'] += $paymentInfo['providers']['material'][$year][$month];
            $paymentInfo['service']['service'][$year]['total'] += $paymentInfo['service']['service'][$year][$month];

            $paymentInfo['salary_workers'][$year]['total'] += $paymentInfo['salary_workers'][$year][$month];
            $paymentInfo['salary_itr'][$year]['total'] += $paymentInfo['salary_itr'][$year][$month];
            $paymentInfo['salary_taxes'][$year]['total'] += $paymentInfo['salary_taxes'][$year][$month];
            $paymentInfo['transfer'][$year]['total'] += $paymentInfo['transfer'][$year][$month];
            $paymentInfo['general_costs'][$year]['total'] += $paymentInfo['general_costs'][$year][$month];
            $paymentInfo['accrued_taxes'][$year]['total'] += $paymentInfo['accrued_taxes'][$year][$month];

            $paymentInfo['total'][$year][$month] = $contractorsMaterial + $contractorsRad + $providersMaterial + $serviceService + $salaryWorkers + $salaryItr + $salaryTaxes + $transfer + $generalCosts + $accruedTaxes;
            $paymentInfo['total'][$year]['total'] += $paymentInfo['total'][$year][$month];
        }

        return compact('receiveInfo', 'paymentInfo', 'monthsFull', 'months');
    }
}
