<?php

namespace App\Models\Helpdesk;

use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Traits\HasBank;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Answer extends Model implements Audit, HasMedia
{
    use SoftDeletes, HasUser, HasStatus, InteractsWithMedia, Auditable;

    protected $table = 'helpdesk_ticket_answers';

    protected $fillable = [
        'ticket_id', 'reply_answer_id', 'text', 'created_by_user_id', 'updated_by_user_id'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function replyAnswer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'reply_answer_id');
    }
}
