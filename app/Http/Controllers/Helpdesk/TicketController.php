<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Helpdesk\StoreTicketRequest;
use App\Models\Helpdesk\Priority;
use App\Models\Helpdesk\Ticket;
use App\Services\Helpdesk\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(Request $request): View
    {
        $tickets = $this->ticketService->filterTicket($request->toArray());
        return view('helpdesk.tickets.index', compact('tickets'));
    }

    public function create(Request $request): View
    {
        $priorities = Priority::getPriorities();
        return view('helpdesk.tickets.create', compact('priorities'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->ticketService->createTicket($request->toArray());
        return redirect()->route('helpdesk.tickets.index');
    }

    public function show(Ticket $ticket): View
    {
        return view('helpdesk.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket): View
    {
        $priorities = Priority::getPriorities();
        return view('helpdesk.tickets.edit', compact('ticket', 'priorities'));
    }

    public function update(Ticket $ticket, Request $request): RedirectResponse
    {
        $this->ticketService->updateTicket($ticket, $request->toArray());
        return redirect()->route('helpdesk.tickets.index');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->ticketService->destroyTicket($ticket);
        return redirect()->route('helpdesk.tickets.index');
    }
}
