<?php

namespace App\Listeners\Helpdesk\Ticket;

use App\Events\Helpdesk\Ticket\TicketCreated;
use App\Events\Helpdesk\Ticket\TicketUpdated;
use App\Services\Helpdesk\TicketViewNotificationService;

class ViewNotificationSubscriber
{
    private TicketViewNotificationService $viewNotificationService;

    public function __construct(TicketViewNotificationService $viewNotificationService)
    {
        $this->viewNotificationService = $viewNotificationService;
    }

    public function handleCreateTicket($event): void
    {
        $this->viewNotificationService->notificationForCreate($event->ticket);
    }

    public function handleUpdateTicket($event): void
    {
        $this->viewNotificationService->notificationForUpdate($event->ticket);
    }

    public function subscribe(): array
    {
        return [
            TicketCreated::class => 'handleCreateTicket',
            TicketUpdated::class => 'handleUpdateTicket',
        ];
    }
}
