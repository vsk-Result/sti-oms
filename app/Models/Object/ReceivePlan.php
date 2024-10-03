<?php

namespace App\Models\Object;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class ReceivePlan extends Model implements Audit
{
    use HasUser, Auditable, HasStatus;

    protected $table = 'object_receive_plan';

    protected $fillable = ['object_id', 'reason_id', 'date', 'amount', 'created_by_user_id', 'updated_by_user_id', 'status_id'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    const REASON_AVANS = 0;
    const REASON_TARGET_AVANS = 1;
    const REASON_COMPLETED_WORKS = 2;
    const REASON_GU = 3;
    const REASON_OTHER = 4;

    public static function getReasons(): array
    {
        return [
            self::REASON_AVANS => 'Аванс',
            self::REASON_TARGET_AVANS => 'Целевой аванс',
            self::REASON_COMPLETED_WORKS => 'Выполненные работы',
            self::REASON_GU => 'Гарантийное удержание',
            self::REASON_OTHER => 'Прочие поступления (ген.подрядные работы и прочие)',
        ];
    }

    public function getReason(): string
    {
        return $this->getReasons()[$this->reason_id];
    }
}
