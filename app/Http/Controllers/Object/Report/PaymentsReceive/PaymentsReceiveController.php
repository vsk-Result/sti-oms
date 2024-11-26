<?php

namespace App\Http\Controllers\Object\Report\PaymentsReceive;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class PaymentsReceiveController extends Controller
{
    public function index(BObject $object): View
    {
        $info = [];
        $infoForChartByDay = [];
        $infoForChartByMonth = [];

        $capital = 0;
        $currentMonth = null;
        $monthInfo = [
            'open' => 0,
            'close' => 0,
        ];

        foreach ($object->payments()->orderBy('date')->get()->groupBy('date') as $date => $payments) {
            $pay = round($payments->where('amount', '<', 0)->sum('amount'));
            $receive = round($payments->where('amount', '>=', 0)->sum('amount'));
            $balance = $pay + $receive;
            $capital += $balance;

            $infoForChartByDay[] = [
                'date' => Carbon::parse($date)->getTimestampMs(),
                'open' => $receive,
                'close' => abs($pay),
            ];

            $month = Carbon::parse($date)->format('Y-m');

            if ($currentMonth !== $month) {
                if (! is_null($currentMonth)) {
                    $infoForChartByMonth[] = array_merge($monthInfo, ['date' => Carbon::parse($currentMonth . '-01')->getTimestampMs()]);
                }

                $currentMonth = $month;
                $monthInfo = [
                    'open' => 0,
                    'close' => 0,
                ];
            } else {
                $monthInfo['open'] += $receive;
                $monthInfo['close'] += abs($pay);
            }

            $info[$date] = compact('date','pay', 'receive', 'balance', 'capital');
        }

        $infoForChart = json_encode($infoForChartByDay);
        $infoForChartByMonth = json_encode($infoForChartByMonth);

        return view('objects.tabs.reports.payments_receive', compact('object', 'info', 'infoForChart', 'infoForChartByMonth'));
    }
}
