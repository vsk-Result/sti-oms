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
use Illuminate\Support\Facades\Cache;

class PaymentReceiveReportService
{
    public function getReportInfo(BObject $object): array
    {
        $year = 2025;
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

        $receiveInfo = [];
        $receiveInfo['material'][$year]['total'] = 0;
        $receiveInfo['rad'][$year]['total'] = 0;
        $receiveInfo['service'][$year]['total'] = 0;

        foreach ($months as $month) {
            $period = [$year . '-' . $month . '-01', $year . '-' . $month . '-31'];
            $receiveInfo['material'][$year][$month] = $object->acts()
                ->whereBetween('date', $period)->sum('amount');
            $receiveInfo['rad'][$year][$month] = $object->acts()
                ->whereBetween('date', $period)->sum('rad_amount');
            $receiveInfo['service'][$year][$month] = $object->acts()
                ->whereBetween('date', $period)->sum('opste_amount');

            $receiveInfo['material'][$year]['total'] += $receiveInfo['material'][$year][$month];
            $receiveInfo['rad'][$year]['total'] += $receiveInfo['rad'][$year][$month];
            $receiveInfo['service'][$year]['total'] += $receiveInfo['service'][$year][$month];
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

//            $paymentInfo['salary_workers'][$year][$month] = 0;
            $paymentInfo['salary_workers'][$year][$month] = (float) WorkhourPivot::where('date', $year . '-' . $month)
                            ->where('is_main', true)
                            ->where('code', $object->code)
                            ->sum('amount');

            $paymentInfo['salary_itr'][$year][$month] = $itrAmount;

            $paymentInfo['salary_taxes'][$year][$month] = 0;

            $paymentInfo['transfer'][$year][$month] = $transferData[$object->id]['transfer_amount'] ?? 0;

            $paymentInfo['general_costs'][$year][$month] = $generalAmount * ($financeReportHistory['general_balance_to_receive_percentage'] ?? 0);

            $paymentInfo['accrued_taxes'][$year][$month] = AccruedTax::whereBetween('date', $period)->sum('amount') * ($workhoursPercents[$year . '-' . $month][$object->code] ?? 0);

            $paymentInfo['salary_workers'][$year]['total'] += $paymentInfo['salary_workers'][$year][$month];
            $paymentInfo['salary_itr'][$year]['total'] += $paymentInfo['salary_itr'][$year][$month];
            $paymentInfo['salary_taxes'][$year]['total'] += $paymentInfo['salary_taxes'][$year][$month];
            $paymentInfo['transfer'][$year]['total'] += $paymentInfo['transfer'][$year][$month];
            $paymentInfo['general_costs'][$year]['total'] += $paymentInfo['general_costs'][$year][$month];
            $paymentInfo['accrued_taxes'][$year]['total'] += $paymentInfo['accrued_taxes'][$year][$month];
        }

        return compact('receiveInfo', 'paymentInfo', 'monthsFull');
    }
}
