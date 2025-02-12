<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Exports\Pivot\CashFlow\Export;
use App\Http\Controllers\Controller;
use App\Services\ReceivePlanService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function store(Request $request): BinaryFileResponse
    {
//        if (auth()->id() === 1) {
//            $periods = $this->receivePlanService->getPeriods();
//            $planPaymentTypes = $this->receivePlanService->getPlanPaymentTypes();
//            foreach ($periods as $index => $period) {
//                foreach ($planPaymentTypes as $paymentType) {
//                    $planPayment = PlanPayment::where('name', $paymentType)->first();
//                    if (!$planPayment) {
//                        continue;
//                    }
//                    $entry = PlanPaymentEntry::where('payment_id', $planPayment->id)->where('date', $period['start'])->first();
//
//                    $amount = TaxPlanItem::where('paid', false)->where('name', $paymentType)->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
//                    if ($index === 0) {
//                        $amount = TaxPlanItem::where('paid', false)->where('name', $paymentType)->where('due_date', '<=', $period['end'])->sum('amount');
//                    }
//
//                    if (!$entry) {
//                        PlanPaymentEntry::create([
//                            'payment_id' => $planPayment->id,
//                            'date' => $period['start'],
//                            'amount' => $amount,
//                            'status_id' => Status::STATUS_ACTIVE
//                        ]);
//                    } else {
//                        $entry->update([
//                            'amount' => $amount,
//                        ]);
//                    }
//                }
//            }
//        }
        return Excel::download(new Export($this->receivePlanService, $request->all()), 'Отчет CASH FLOW.xlsx');
    }
}
