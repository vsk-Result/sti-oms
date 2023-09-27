<?php

namespace App\Services\Helpdesk;

use App\Models\BankGuarantee;
use App\Models\Currency;
use App\Models\Helpdesk\Ticket;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    public function filterTicket(array $requestData, bool $needPaginate = true): LengthAwarePaginator|Collection
    {
        $query = Ticket::query();

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        return $needPaginate ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function createTicket(array $requestData): void
    {
        $ticket = Ticket::create([
            'priority_id' => $requestData['priority_id'],
            'title' => $requestData['title'],
            'content' => $requestData['content'],
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $ticket->addMedia($file)->toMediaCollection();
            }
        }
    }

    public function updateTicket(Ticket $ticket, array $requestData): void
    {
        $dataToUpdate = [];

        foreach ($ticket->getFillable() as $fillable) {
            if (isset($requestData[$fillable])) {
                $dataToUpdate[$fillable] = $requestData[$fillable];
            }
        }

        $ticket->update($dataToUpdate);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $ticket->addMedia($file)->toMediaCollection();
            }
        }
    }

    public function destroyTicket(Ticket $ticket): void
    {
        $ticket->delete();
    }
}
