<?php

namespace App\Http\Controllers\Object\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Object\CashFlow\CashFlowReceiveService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CashFlowReceiveController extends Controller
{
    public function __construct(private CashFlowReceiveService $cashFlowReceiveService) { }

    public function store(BObject $object, Request $request): RedirectResponse
    {
        $requestDate = array_merge($request->toArray(), ['object_id' => $object->id, 'date' => Carbon::parse($request->get('date'))->startOfWeek()->format('Y-m-d')]);
        $this->cashFlowReceiveService->createReceive($requestDate);

        return redirect()->back();
    }
}
