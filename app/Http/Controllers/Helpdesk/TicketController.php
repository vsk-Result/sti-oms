<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Helpdesk\StoreTicketRequest;
use App\Models\Helpdesk\Priority;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketRead;
use App\Models\Object\BObject;
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
        $total = [];
        $tickets = $this->ticketService->filterTicket($request->toArray(), $total);

        $openTicketsCount = $total['open_tickets_count'];
        $closeTicketsCount = $total['close_tickets_count'];
        $groupedByPriorities = $total['grouped_by_priorities'];
        $groupedByObjects = $total['grouped_by_objects'];
        $groupedByUsers = $total['grouped_by_users'];

        return view(
            'helpdesk.tickets.index',
            compact(
                'tickets',
                'openTicketsCount',
                'closeTicketsCount',
                'groupedByPriorities',
                'groupedByObjects',
                'groupedByUsers',
            )
        );
    }

    public function create(): View
    {
        $priorities = Priority::getPriorities();
        $objects = BObject::orderBy('code')->get();
        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        return view('helpdesk.tickets.create', compact('priorities', 'objects'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $this->ticketService->createTicket($request->toArray());
        return redirect()->route('helpdesk.tickets.index');
    }

    public function show(Ticket $ticket): View
    {
        TicketRead::where('ticket_id', $ticket->id)->where('receiver_user_id', auth()->id())->delete();
        return view('helpdesk.tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket): View
    {
        $priorities = Priority::getPriorities();
        $objects = BObject::orderBy('code')->get();
        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        return view('helpdesk.tickets.edit', compact('ticket', 'priorities', 'objects'));
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
