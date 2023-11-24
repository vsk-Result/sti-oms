<?php

namespace App\Listeners\Helpdesk\Ticket;

use App\Events\Helpdesk\Ticket\TicketClosed;
use App\Events\Helpdesk\Ticket\TicketCreated;
use App\Events\Helpdesk\Ticket\TicketDeleted;
use App\Events\Helpdesk\Ticket\TicketOpened;
use App\Events\Helpdesk\Ticket\TicketUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TicketEventSubscriber
{
    // Временный хардкод, если вдруг потом можно будет назначать других пользователь
    private string $assignTo = 'result007@yandex.ru';

    // Пока что всегда будет Оксана для контроля
    private string $alwaysCopyTo = 'oksana.dashenko@st-ing.com';

    public function handleCreateTicket($event): void
    {
        $ticket = $event->ticket;
        $ticketAuthor = $ticket->createdBy()->first();
        $emailSubject = 'Новое обращение: ' . $ticket->title;

        try {
            Mail::send('emails.helpdesk.tickets.created', compact('ticket', 'ticketAuthor'), function ($m) use ($ticketAuthor, $emailSubject) {
                $m->from('support@st-ing.com', 'OMS Helpdesk');
                $m->to($this->assignTo);
//                $m->cc($this->alwaysCopyTo);
                $m->cc($ticketAuthor->email)
                    ->subject($emailSubject);
            });
        } catch (\Exception $e) {
            Log::channel('custom_events_log')->debug('[TicketCreated] Не удалось отправить уведомление на email: "' . $e->getMessage());
        }
    }

    public function handleUpdateTicket($event): void
    {
        $ticket = $event->ticket;
        $ticketAuthor = $ticket->createdBy()->first();
        $updatedBy = $event->updatedBy;
        $emailSubject = 'Обращение #' . $ticket->id . ' изменено';

        try {
            Mail::send('emails.helpdesk.tickets.updated', compact('ticket', 'ticketAuthor', 'updatedBy'), function ($m) use ($ticketAuthor, $emailSubject) {
                $m->from('support@st-ing.com', 'OMS Helpdesk');
                $m->to($ticketAuthor->email);
                $m->cc($this->assignTo)
                    ->subject($emailSubject);
            });
        } catch (\Exception $e) {
            Log::channel('custom_events_log')->debug('[TicketUpdated] Не удалось отправить уведомление на email: "' . $e->getMessage());
        }
    }

    public function handleDeleteTicket($event): void
    {
        $ticket = $event->ticket;
        $ticketAuthor = $ticket->createdBy()->first();
        $deletedBy = $event->deletedBy;
        $emailSubject = 'Обращение #' . $ticket->id . ' удалено';

        try {
            Mail::send('emails.helpdesk.tickets.deleted', compact('ticket', 'ticketAuthor', 'deletedBy'), function ($m) use ($ticketAuthor, $emailSubject) {
                $m->from('support@st-ing.com', 'OMS Helpdesk');
                $m->to($ticketAuthor->email);
                $m->cc($this->assignTo)
                    ->subject($emailSubject);
            });
        } catch (\Exception $e) {
            Log::channel('custom_events_log')->debug('[TicketDeleted] Не удалось отправить уведомление на email: "' . $e->getMessage());
        }
    }

    public function handleOpenTicket($event): void
    {
        $ticket = $event->ticket;
        $ticketAuthor = $ticket->createdBy()->first();
        $openedBy = $event->openedBy;
        $emailSubject = 'Обращение #' . $ticket->id . ' повторно открыто';

        try {
            Mail::send('emails.helpdesk.tickets.opened', compact('ticket', 'ticketAuthor', 'openedBy'), function ($m) use ($ticketAuthor, $emailSubject) {
                $m->from('support@st-ing.com', 'OMS Helpdesk');
                $m->to($ticketAuthor->email);
                $m->cc($this->assignTo)
                    ->subject($emailSubject);
            });
        } catch (\Exception $e) {
            Log::channel('custom_events_log')->debug('[TicketOpened] Не удалось отправить уведомление на email: "' . $e->getMessage());
        }
    }

    public function handleCloseTicket($event): void
    {
        $ticket = $event->ticket;
        $ticketAuthor = $ticket->createdBy()->first();
        $closedBy = $event->closedBy;
        $emailSubject = 'Обращение #' . $ticket->id . ' закрыто';

        try {
            Mail::send('emails.helpdesk.tickets.closed', compact('ticket', 'ticketAuthor', 'closedBy'), function ($m) use ($ticketAuthor, $emailSubject) {
                $m->from('support@st-ing.com', 'OMS Helpdesk');
                $m->to($ticketAuthor->email);
                $m->cc($this->assignTo)
                    ->subject($emailSubject);
            });
        } catch (\Exception $e) {
            Log::channel('custom_events_log')->debug('[TicketClosed] Не удалось отправить уведомление на email: "' . $e->getMessage());
        }
    }

    public function subscribe(): array
    {
        return [
            TicketCreated::class => 'handleCreateTicket',
            TicketUpdated::class => 'handleUpdateTicket',
            TicketDeleted::class => 'handleDeleteTicket',
            TicketOpened::class => 'handleOpenTicket',
            TicketClosed::class => 'handleCloseTicket',
        ];
    }
}
