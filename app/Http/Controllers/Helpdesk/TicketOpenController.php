<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Helpdesk\Ticket;
use App\Services\Helpdesk\TicketService;
use Illuminate\Http\RedirectResponse;

class TicketOpenController extends Controller
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function store(Ticket $ticket): RedirectResponse
    {
        $this->ticketService->openTicket($ticket);
        return redirect()->back();
    }
}