<?php

namespace App\Services\Object\Report;

use App\Models\AccruedTax\AccruedTax;
use App\Models\CRM\CObject;
use App\Models\CRM\Workhour;
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
        $object27_1 = BObject::where('code', '27.1')->first();

        $financeReportHistory = FinanceReportHistory::getCurrentFinanceReportForObject($object);

        foreach ($months as $month) {
            $period = [$year . '-' . $month . '-01', $year . '-' . $month . '-31'];

            $workhoursPercents = [];
            $objectIdsOnQuarts = [];
            $workhours = Workhour::whereBetween('date', $period)->get();
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
                $workhoursPercents[$year . '-' .$month][$oCode] = $hours / $sum;
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
            $generalAmount = (clone $paymentQuery)->whereNotIn('code', ['7.1', '7.2', '7.5'])->where('type_id', Payment::TYPE_GENERAL)->sum('amount')
                + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount');

            $transferData = ObjectService::getDistributionTransferServiceByPeriod($period);

            $salaryWorkers = -abs((float) WorkhourPivot::where('date', $year . '-' . $month)
                ->where('is_main', true)
                ->where('code', $object->code)
                ->sum('amount'));

            $salaryItr = -abs($itrAmount);
            $salaryTaxes = -abs(0);
            $transfer = -abs($transferData[$object->id]['transfer_amount'] ?? 0);
            $generalCosts = -abs($generalAmount * ($financeReportHistory['general_balance_to_receive_percentage'] ?? 0));
            $accruedTaxes = -abs(AccruedTax::whereBetween('date', $period)->sum('amount') * ($workhoursPercents[$year . '-' . $month][$object->code] ?? 0));

            $paymentInfo['salary_workers'][$year][$month] = $salaryWorkers;
            $paymentInfo['salary_itr'][$year][$month] = $salaryItr;
            $paymentInfo['salary_taxes'][$year][$month] = $salaryTaxes;
            $paymentInfo['transfer'][$year][$month] = $transfer;
            $paymentInfo['general_costs'][$year][$month] = $generalCosts;
            $paymentInfo['accrued_taxes'][$year][$month] = $accruedTaxes;

            $paymentInfo['salary_workers'][$year]['total'] += $paymentInfo['salary_workers'][$year][$month];
            $paymentInfo['salary_itr'][$year]['total'] += $paymentInfo['salary_itr'][$year][$month];
            $paymentInfo['salary_taxes'][$year]['total'] += $paymentInfo['salary_taxes'][$year][$month];
            $paymentInfo['transfer'][$year]['total'] += $paymentInfo['transfer'][$year][$month];
            $paymentInfo['general_costs'][$year]['total'] += $paymentInfo['general_costs'][$year][$month];
            $paymentInfo['accrued_taxes'][$year]['total'] += $paymentInfo['accrued_taxes'][$year][$month];

            $paymentInfo['total'][$year][$month] = $salaryWorkers + $salaryItr + $salaryTaxes + $transfer + $generalCosts + $accruedTaxes;
            $paymentInfo['total'][$year]['total'] += $paymentInfo['total'][$year][$month];
        }

        return compact('receiveInfo', 'paymentInfo', 'monthsFull', 'months');
    }
}
