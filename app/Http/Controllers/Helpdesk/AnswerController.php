<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Helpdesk\Ticket;
use App\Services\Helpdesk\AnswerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    private AnswerService $answerService;

    public function __construct(AnswerService $answerService)
    {
        $this->answerService = $answerService;
    }

    public function store(Ticket $ticket, Request $request): RedirectResponse
    {
        $requestData = $request->toArray();
        $requestData['ticket_id'] = $ticket->id;

        $this->answerService->createAnswer($requestData);

        return redirect()->back();
    }
}
