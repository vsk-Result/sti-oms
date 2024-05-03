<?php

namespace App\Http\Controllers\Finance\DistributionTransferService;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Models\Payment;
use Illuminate\View\View;

class DistributionTransferServiceController extends Controller
{
    public function index(): View
    {
        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objects = BObject::whereIn('code', $codes)->orderByDesc('code')->get();

        return view('distribution-transfer-service.index', compact('objects'));
    }
}
