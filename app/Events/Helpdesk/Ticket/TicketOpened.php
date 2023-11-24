<?php

namespace App\Events\Helpdesk\Ticket;

use App\Models\Helpdesk\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketOpened
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public User $openedBy;

    public function __construct(Ticket $ticket, User $openedBy)
    {
        $this->ticket = $ticket;
        $this->openedBy = $openedBy;
    }
}
