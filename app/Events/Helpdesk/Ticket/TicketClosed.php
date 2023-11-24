<?php

namespace App\Events\Helpdesk\Ticket;

use App\Models\Helpdesk\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketClosed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public User $closedBy;

    public function __construct(Ticket $ticket, User $closedBy)
    {
        $this->ticket = $ticket;
        $this->closedBy = $closedBy;
    }
}
