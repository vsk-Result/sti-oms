<?php

namespace App\Services\Helpdesk;

use App\Events\Helpdesk\Ticket\Answer\AnswerCreated;
use App\Models\Helpdesk\Answer;
use App\Models\Status;

class AnswerService
{
    public function createAnswer(array $requestData): void
    {
        $answer = Answer::create([
            'ticket_id' => $requestData['ticket_id'],
            'reply_answer_id' => $requestData['reply_answer_id'] ?? null,
            'text' => $requestData['text'],
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $answer->addMedia($file)->toMediaCollection();
            }
        }

        AnswerCreated::dispatch($answer);
    }
}
