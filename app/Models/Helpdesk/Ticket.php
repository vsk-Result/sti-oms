<?php

namespace App\Models\Helpdesk;

use App\Models\Object\BObject;
use App\Models\Status;
use App\Models\User;
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
        'updated_by_user_id', 'content', 'priority_id', 'object_id', 'status_id', 'time_to_complete',
        'assign_user_id'
    ];

    const PREVIEW_TITLE_TEXT_LENGTH = 65;
    const PREVIEW_CONTENT_TEXT_LENGTH = 180;

    private function getStatusesList(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Открыто',
            Status::STATUS_BLOCKED => 'Закрыто',
            Status::STATUS_DELETED => 'Удалено'
        ];
    }

    public function assignTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assign_user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'ticket_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function getPriority(): object
    {
        return Priority::getPriority($this->priority_id);
    }

    public function getPreviewContent(): string
    {
        if (empty($this->content)) {
            return '';
        }

        return mb_strlen($this->content) > self::PREVIEW_CONTENT_TEXT_LENGTH
            ? mb_substr($this->content, 0, self::PREVIEW_CONTENT_TEXT_LENGTH) . '...'
            : $this->content;
    }

    public function getPreviewTitle(): string
    {
        return mb_strlen($this->title) > self::PREVIEW_TITLE_TEXT_LENGTH
            ? mb_substr($this->title, 0, self::PREVIEW_TITLE_TEXT_LENGTH) . '...'
            : $this->title;
    }

    public function getAnswersCountPlural(): string
    {
        return $this->answers->count() . trans_choice(' Ответ | Ответа | Ответов', $this->answers->count());
    }

    public function getExecutionLeft(): string
    {
        return empty($this->execution_date) ? "" : Carbon::parse($this->execution_date)->diffForHumans();
    }

    public function scopeActive($query)
    {
        return $query->where('status_id', Status::STATUS_ACTIVE);
    }

    public function scopeClosed($query)
    {
        return $query->where('status_id', Status::STATUS_BLOCKED);
    }

    public function isClosed(): bool
    {
        return $this->status_id === Status::STATUS_BLOCKED;
    }

    public function isOpened(): bool
    {
        return $this->status_id === Status::STATUS_ACTIVE;
    }

    public function getObjectName(): string
    {
        $object = $this->object;
        return $object ? $object->getName() : 'Общее';
    }

    public function haveUnreadUpdates(): bool
    {
        return TicketRead::where('ticket_id', $this->id)->where('receiver_user_id', auth()->id())->exists();
    }
}
