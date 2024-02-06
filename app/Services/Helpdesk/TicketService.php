<?php

namespace App\Services\Helpdesk;

use App\Events\Helpdesk\Ticket\TicketClosed;
use App\Events\Helpdesk\Ticket\TicketCreated;
use App\Events\Helpdesk\Ticket\TicketDeleted;
use App\Events\Helpdesk\Ticket\TicketOpened;
use App\Events\Helpdesk\Ticket\TicketUpdated;
use App\Events\Helpdesk\Ticket\TicketWaiting;
use App\Models\Helpdesk\Priority;
use App\Models\Helpdesk\Ticket;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    public function filterTicket(array $requestData, array &$total, bool $needPaginate = true): LengthAwarePaginator|Collection
    {
        $totalQuery = Ticket::query();
        $query = Ticket::query();

//        if (! auth()->user()->hasRole('super-admin')) {
//            $totalQuery->where('created_by_user_id', auth()->id());
//            $query->where('created_by_user_id', auth()->id());
//        }

        if (! empty($requestData['priority_id'])) {
            $query->whereIn('priority_id', $requestData['priority_id']);
        }

        if (! empty($requestData['object_id'])) {
            if (in_array('general', $requestData['object_id'])) {
                $query->whereNull('object_id');
            } else {
                $query->whereIn('object_id', $requestData['object_id']);
            }
        }

        if (! empty($requestData['user_id'])) {
            $query->whereIn('created_by_user_id', $requestData['user_id']);
        }

        if (! empty($requestData['status_id'])) {
            $query->whereIn('status_id', $requestData['status_id']);
        }

        $perPage = 30;
        if (! empty($requestData['count_per_page'])) {
            $perPage = (int) preg_replace("/[^0-9]/", '', $requestData['count_per_page']);
        }

        $total['open_tickets_count'] = (clone $totalQuery)->active()->count();
        $total['close_tickets_count'] = (clone $totalQuery)->closed()->count();
        $total['waiting_tickets_count'] = (clone $totalQuery)->waiting()->count();

        $total['grouped_by_objects'] = [];
        foreach ((clone $totalQuery)->active()->get()->groupBy('object_id') as $objectId => $tickets) {
            if ($objectId === '') {
                $objectId = 'general';
                $objectName = 'Общее';
            } else {
                $object = BObject::find($objectId);

                if (!$object) {
                    continue;
                }

                $objectName = $object->getName();
            }

            $total['grouped_by_objects'][] = [
                'object_id' => $objectId,
                'object_name' => $objectName,
                'tickets_count' => $tickets->count(),
            ];
        }

        $total['grouped_by_priorities'] = [];
        foreach (Priority::getPriorities() as $priority) {
            $total['grouped_by_priorities'][] = [
                'priority_id' => $priority->id,
                'priority_name' => $priority->name,
                'tickets_count' => (clone $totalQuery)->active()->where('priority_id', $priority->id)->count(),
            ];
        }

        $total['grouped_by_users'] = [];
        foreach (Ticket::active()->get()->groupBy('created_by_user_id') as $userId => $tickets) {
            $user = User::find($userId);

            $total['grouped_by_users'][] = [
                'user_id' => $userId,
                'user_name' => $user->name,
                'tickets_count' => $tickets->count(),
            ];
        }

        $query->with( 'answers');
        $query->orderByDesc('created_at');

        return $needPaginate ? $query->paginate($perPage)->withQueryString() : $query->get();
    }

    public function createTicket(array $requestData): void
    {
        $ticket = Ticket::create([
            'assign_user_id' => $requestData['assign_user_id'],
            'priority_id' => $requestData['priority_id'] ?? Priority::NOT_SELECTED_ID,
            'object_id' => $requestData['object_id'] === 'null' ? null : $requestData['object_id'],
            'title' => $requestData['title'],
            'content' => $requestData['content'],
            'time_to_complete' => $requestData['time_to_complete'] ?? null,
            'status_id' => Status::STATUS_ACTIVE,
        ]);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $ticket->addMedia($file)->toMediaCollection();
            }
        }

        TicketCreated::dispatch($ticket);
    }

    public function updateTicket(Ticket $ticket, array $requestData): void
    {
        $dataToUpdate = [];

        foreach ($ticket->getFillable() as $fillable) {
            if (isset($requestData[$fillable])) {
                $dataToUpdate[$fillable] = $requestData[$fillable] === 'null' ? null : $requestData[$fillable];
            }
        }

        $ticket->update($dataToUpdate);

        if (! empty($requestData['files'])) {
            foreach ($requestData['files'] as $file) {
                $ticket->addMedia($file)->toMediaCollection();
            }
        }

        TicketUpdated::dispatch($ticket, auth()->user());
    }

    public function destroyTicket(Ticket $ticket): void
    {
        TicketDeleted::dispatch($ticket, auth()->user());

        $ticket->delete();
    }

    public function openTicket(Ticket $ticket): void
    {
        $ticket->update([
            'complete_date' => null,
            'status_id' => Status::STATUS_ACTIVE
        ]);

        TicketOpened::dispatch($ticket, auth()->user());
    }

    public function closeTicket(Ticket $ticket): void
    {
        $ticket->update([
            'complete_date' => Carbon::now(),
            'status_id' => Status::STATUS_BLOCKED
        ]);

        TicketClosed::dispatch($ticket, auth()->user());
    }

    public function waitingTicket(Ticket $ticket): void
    {
        $ticket->update([
            'complete_date' => Carbon::now(),
            'status_id' => Status::STATUS_WAITING
        ]);

        TicketWaiting::dispatch($ticket, auth()->user());
    }
}
