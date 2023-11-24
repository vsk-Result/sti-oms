<?php

namespace App\Events\Helpdesk\Ticket;

use App\Models\Helpdesk\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public User $updatedBy;

    public function __construct(Ticket $ticket, User $updatedBy)
    {
        $this->ticket = $ticket;
        $this->updatedBy = $updatedBy;
    }
}
