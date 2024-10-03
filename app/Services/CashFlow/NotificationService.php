<?php

namespace App\Services\CashFlow;

use App\Models\CashFlow\Notification;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    public function createNotification($typeId, $eventTypeId, $name, $description = null): void
    {
        foreach ($this->getTargetUserIds() as $targetUserId) {
            Notification::create([
                'target_user_id' => $targetUserId,
                'type_id' => $typeId,
                'event_type_id' => $eventTypeId,
                'name' => $name,
                'description' => $description,
                'read_date' => null,
                'status_id' => Status::STATUS_ACTIVE
            ]);
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
