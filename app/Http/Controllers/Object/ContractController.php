<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ActService;
use App\Services\Contract\ContractService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    private ContractService $contractService;

    public function __construct(ContractService $contractService, ActService $actService)
    {
        $this->contractService = $contractService;
        $this->actService = $actService;
    }

    public function index(BObject $object, Request $request): View
    {
        $total = [];
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());
        $contracts = $this->contractService->filterContracts($requestData, $total);
        $objects = BObject::orderBy('code')->get();

        $actsPaymentsLineChartInfo = $this->actService->prepareInfoForActsPaymentsLineChart($total['act_ids']);

        return view('objects.tabs.contracts', compact('object', 'contracts', 'objects', 'total', 'actsPaymentsLineChartInfo'));
    }
}
