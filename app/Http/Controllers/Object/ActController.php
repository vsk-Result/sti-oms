<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ActService;
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

        $actsPaymentsLineChartInfo = $this->actService->prepareInfoForActsPaymentsLineChart($total['ids']);

        $paymentQuery = Payment::select('object_id', 'amount');
        $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

        $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
        $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
        $object->total_balance = $object->total_pay + $object->total_receive;
        $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');

        return view('objects.tabs.acts', compact('object', 'acts', 'total', 'objects', 'contracts', 'actsPaymentsLineChartInfo'));
    }
}
