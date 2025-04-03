<?php

namespace App\Http\Controllers\Object;

use App\Exports\Object\ReceivePlan\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Services\ReceivePlanService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReceivePlanExportController extends Controller
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function store(BObject $object): BinaryFileResponse
    {
        $reasons = ReceivePlan::getReasons();
        $periods = $this->receivePlanService->getPeriods($object->id);
        $plans = $this->receivePlanService->getPlans($object->id, $periods[0]['start'], end($periods)['start']);
        $cfPayments = $this->receivePlanService->getCFPayments($object->id, $periods);

        return Excel::download(new Export($reasons, $periods, $plans, $cfPayments), $object->code . '_Cash_flow.xlsx');
    }
}
