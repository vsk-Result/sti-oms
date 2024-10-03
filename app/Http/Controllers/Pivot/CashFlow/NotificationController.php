<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Services\CashFlow\NotificationService;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function update(): RedirectResponse
    {
        $this->notificationService->readAllNewNotifications();

        return redirect()->back();
    }
}
