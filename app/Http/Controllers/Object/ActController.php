<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function index(BObject $object, Request $request): View
    {
        $total = [];
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());
        $acts = $this->actService->filterActs($requestData, $total);
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();

        $actsMonths = [];
        foreach (ActPayment::whereIn('act_id', $total['ids'])->orderBy('date')->get() as $payment) {
            if (empty($payment->date)) {
                continue;
            }

            $month = Carbon::parse($payment->date)->format('F Y');
            if (! isset($actsMonths[$month][$payment->currency])) {
                $actsMonths[$month][$payment->currency] = 0;
            }
            $actsMonths[$month][$payment->currency] += $payment->amount;
        }
        foreach ($actsMonths as $k => $currencies) {
            foreach ($currencies as $currency => $amount) {
                $actsMonths[$k][$currency] = $amount;
            }
        }
        $RUBActsAmounts = [];

        foreach ($actsMonths as $currencies) {
            $RUBActsAmounts[] = $currencies['RUB'];
        }

        $actsMonths = array_keys($actsMonths);

        return view('objects.tabs.acts', compact('object', 'acts', 'total', 'objects', 'contracts', 'RUBActsAmounts', 'actsMonths'));
    }
}
