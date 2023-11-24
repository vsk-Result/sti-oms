<?php

namespace App\Events\Helpdesk\Ticket;

use App\Models\Helpdesk\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public User $deletedBy;

    public function __construct(Ticket $ticket, User $deletedBy)
    {
        $this->ticket = $ticket;
        $this->deletedBy = $deletedBy;
    }
}
