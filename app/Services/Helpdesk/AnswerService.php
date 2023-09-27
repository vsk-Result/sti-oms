<?php

namespace App\Services\Helpdesk;

use App\Models\BankGuarantee;
use App\Models\Currency;
use App\Models\Helpdesk\Answer;
use App\Models\Helpdesk\Ticket;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
    }
}
