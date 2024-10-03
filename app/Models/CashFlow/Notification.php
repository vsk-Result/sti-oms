<?php

namespace App\Models\CashFlow;

use App\Models\User;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Notification extends Model implements Audit
{
    use HasUser, HasStatus, Auditable;

    protected $table = 'cash_flow_notifications';

    protected $fillable = [
        'created_by_user_id', 'updated_by_user_id', 'target_user_id', 'name', 'description', 'type_id', 'event_type_id', 'status_id', 'read_date'
    ];

    const TYPE_RECEIVE = 0;
    const TYPE_PAYMENT = 1;

    const EVENT_TYPE_CREATE = 0;
    const EVENT_TYPE_UPDATE = 1;
    const EVENT_TYPE_DELETE = 2;

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function getEventStatusColor(): string
    {
        return [
            self::EVENT_TYPE_CREATE => '#9deac2',
            self::EVENT_TYPE_UPDATE => '#f5e5a7',
            self::EVENT_TYPE_DELETE => '#ee7d83',
        ][$this->event_type_id];
    }
}
