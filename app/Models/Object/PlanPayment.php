<?php

namespace App\Models\Object;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class PlanPayment extends Model implements Audit
{
    use HasUser, Auditable;

    protected $table = 'object_plan_payments';

    protected $fillable = ['object_id', 'amount', 'field', 'type_id', 'created_by_user_id', 'updated_by_user_id'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    const TYPE_AUTO = 0;
    const TYPE_MANUAL = 1;

    public static function getTypes(): array
    {
        return [
            self::TYPE_AUTO => 'Автоматический',
            self::TYPE_MANUAL => 'Ручной',
        ];
    }

    public function isAutoCalculation(): bool
    {
        return $this->type_id === self::TYPE_AUTO;
    }
}
