<?php

namespace App\Services\Helpdesk;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketRead;

class TicketViewNotificationService
{
    const SUPER_ADMINS = [1, 8];

    public function notificationForCreate(Ticket $ticket): void
    {
        // Пока что уведомляем только супер пользователей
        foreach (self::SUPER_ADMINS as $adminId) {
            if ($adminId !== $ticket->created_by_user_id) {
                TicketRead::create([
                    'ticket_id' => $ticket->id,
                    'sender_user_id' => $ticket->created_by_user_id,
                    'receiver_user_id' => $adminId,
                ]);
            }
        }
    }

    public function notificationForUpdate(Ticket $ticket): void
    {
        $currentUserId = auth()->id();
        $notifyUsersId = array_merge(self::SUPER_ADMINS, [$currentUserId, $ticket->created_by_user_id]);
        $notifyUsersId = array_unique($notifyUsersId);

        if ($currentUserId !== $ticket->created_by_user_id) {
            foreach ($notifyUsersId as $userId) {
                if ($userId !== $currentUserId) {
                    TicketRead::create([
                        'ticket_id' => $ticket->id,
                        'sender_user_id' => $ticket->created_by_user_id,
                        'receiver_user_id' => $userId,
                    ]);
                }
            }
        } else {
            foreach ($notifyUsersId as $userId) {
                if ($userId !== $ticket->created_by_user_id) {
                    TicketRead::create([
                        'ticket_id' => $ticket->id,
                        'sender_user_id' => $ticket->created_by_user_id,
                        'receiver_user_id' => $userId,
                    ]);
                }
            }
        }
    }
}