<?php

namespace App\Events\Helpdesk\Ticket\Answer;

use App\Models\Helpdesk\Answer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnswerCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Answer $answer;

    public function __construct(Answer $answer)
    {
        $this->answer = $answer;
    }
}
