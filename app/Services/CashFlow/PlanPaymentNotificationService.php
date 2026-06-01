<?php

namespace App\Services\CashFlow;

use App\Models\CashFlow\Notification;
use App\Models\Status;
use App\Services\PlanPaymentService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PlanPaymentNotificationService
{
    private PlanPaymentService $planPaymentService;

    public function __construct(PlanPaymentService $planPaymentService)
    {
        $this->planPaymentService = $planPaymentService;
    }

    public function updateNotification($paymentId): void
    {
        $payment = $this->planPaymentService->findPlanPayment($paymentId);
        if ($payment) {
            $this->planPaymentService->updatePlanPayment(['payment_id' => $paymentId, 'need_notification' => !$payment->need_notification]);
        }
    }

    public function getHistoryNotifications(): Collection
    {
        return Notification::whereNotNull('read_date')->where('target_user_id', auth()->id())->orderByDesc('created_at')->take(50)->get();
    }

    public function getNewNotifications(): Collection
    {
        return Notification::whereNull('read_date')->where('target_user_id', auth()->id())->orderByDesc('created_at')->get();
    }

    public function hasUnreadNotifications(): bool
    {
        return $this->getNewNotifications()->count() > 0;
    }

    public function getTargetUserIds(): array
    {
        return [1, 16, 8];
    }

    public function readAllNewNotifications(): void
    {
        $newNotifications = $this->getNewNotifications();
        foreach ($newNotifications as $notification) {
            $notification->update([
                'read_date' => Carbon::now(),
            ]);
        }
    }
}
