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

class Ticket extends Model implements Audit, HasMedia
{
    use SoftDeletes, HasUser, HasStatus, InteractsWithMedia, Auditable;

    protected $table = 'helpdesk_tickets';

    protected $fillable = [
        'execution_date', 'complete_date', 'title', 'created_by_user_id',
        'updated_by_user_id', 'content', 'priority_id',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'ticket_id');
    }

    public function getPriority(): object
    {
        return Priority::getPriority($this->priority_id);
    }

    public function getPreviewContent(): string
    {
        return mb_strlen($this->content) > 300
            ? mb_substr($this->content, 0, 300) . '...'
            : $this->content;
    }

    public function getPreviewTitle(): string
    {
        return mb_strlen($this->title) > 50
            ? mb_substr($this->title, 0, 50) . '...'
            : $this->title;
    }

    public function getAnswersCountPlural(): string
    {
        return $this->answers->count() . trans_choice('Ответ | Ответа | Ответов', $this->answers->count());
    }
}
