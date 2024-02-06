<?php

namespace App\Events\Helpdesk\Ticket;

use App\Models\Helpdesk\Ticket;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketWaiting
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Ticket $ticket;
    public User $goWaitingBy;

    public function __construct(Ticket $ticket, User $goWaitingBy)
    {
        $this->ticket = $ticket;
        $this->goWaitingBy = $goWaitingBy;
    }
}
