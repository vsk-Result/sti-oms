<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
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

        return view('objects.tabs.acts', compact('object', 'acts', 'total', 'objects', 'contracts', 'actsPaymentsLineChartInfo'));
    }
}
