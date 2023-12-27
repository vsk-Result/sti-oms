<?php

namespace App\Listeners\Helpdesk\Ticket;

use App\Events\Helpdesk\Ticket\Answer\AnswerCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AnswerEventSubscriber
{
    // Временный хардкод, если вдруг потом можно будет назначать других пользователь
    private string $assignTo = 'result007@yandex.ru';

    public function handleCreateAnswer($event): void
    {
        if (config('app.debug') === true) {
            return;
        }

        $answer = $event->answer;
        $ticket = $answer->ticket;
        $ticketAuthor = $ticket->createdBy()->first();
        $answerAuthor = $answer->createdBy()->first();
        $emailSubject = 'Новый ответ на обращение #' . $ticket->id;

        try {
            Mail::send('emails.helpdesk.tickets.answers.created', compact('answer', 'ticket', 'answerAuthor', 'ticketAuthor'), function ($m) use ($ticketAuthor, $answerAuthor, $emailSubject) {
                $m->from('support@st-ing.com', 'OMS Helpdesk');
                $m->to($ticketAuthor->email);
                $m->cc($answerAuthor->email);
                $m->cc($this->assignTo)
                    ->subject($emailSubject);
            });
        } catch (\Exception $e) {
            Log::channel('custom_events_log')->debug('[AnswerCreated] Не удалось отправить уведомление на email: "' . $e->getMessage());
        }
    }

    public function subscribe(): array
    {
        return [
            AnswerCreated::class => 'handleCreateAnswer',
        ];
    }
}
