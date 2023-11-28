<?php

namespace App\Models\Helpdesk;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketRead extends Model
{
    protected $table = 'helpdesk_ticket_reads';

    protected $fillable = ['ticket_id', 'sender_user_id', 'receiver_user_id'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function haveUnreadUpdates(): bool
    {
        return self::where('receiver_user_id', auth()->id())->exists();
    }
}
