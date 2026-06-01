<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Services\CashFlow\PlanPaymentNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanPaymentNotificationController extends Controller
{
    private PlanPaymentNotificationService $planPaymentNotificationService;

    public function __construct(PlanPaymentNotificationService $planPaymentNotificationService)
    {
        $this->planPaymentNotificationService = $planPaymentNotificationService;
    }

    public function update(Request $request): JsonResponse
    {
        $this->planPaymentNotificationService->updateNotification($request->get('payment_id'));
        $status = 'success';
        $message = 'Уведомление обновлено';
        return response()->json(compact('status', 'message'));
    }
}
