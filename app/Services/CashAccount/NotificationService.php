<?php

namespace App\Services\CashAccount;

use App\Models\CashAccount\CashAccount;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    const CACHE_KEY = 'cash_account_notifications';

    public function notify(CashAccount $cashAccount): void
    {
        $notifyData = Cache::get(self::CACHE_KEY, []);

        foreach ($cashAccount->sharedUsers as $sharedUser) {
            $notifyData[$cashAccount->id][$sharedUser->id] = 'unread';
        }

        foreach (User::all() as $user) {
            if ($user->can('index cash-accounts-all-view')) {
                $notifyData[$cashAccount->id][$user->id] = 'unread';
            }
        }

        Cache::put(self::CACHE_KEY, $notifyData);
    }

    public function markAsRead(CashAccount $cashAccount, ?User $user = null): void
    {
        $notifyData = Cache::get(self::CACHE_KEY, []);

        if (isset($notifyData[$cashAccount->id][$user->id]) && $user) {
            unset($notifyData[$cashAccount->id][$user->id]);
        }

        Cache::put(self::CACHE_KEY, $notifyData);
    }

    public function hasUnreadNotifications(User $user, ?CashAccount $cashAccount = null): bool
    {
        $notifyData = Cache::get(self::CACHE_KEY, []);

        if ($cashAccount) {
            return isset($notifyData[$cashAccount->id][$user->id]);
        }

        foreach ($notifyData as $users) {
            foreach ($users as $userId => $mark) {
                if ($userId === $user->id) {
                    return true;
                }
            }
        }

        return false;
    }
}
