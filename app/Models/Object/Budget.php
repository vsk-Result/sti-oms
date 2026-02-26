<?php

namespace App\Models\Object;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Budget extends Model implements Audit
{
    use HasUser, Auditable;

    protected $table = 'object_budgets';

    protected $fillable = ['object_id', 'amount', 'name', 'type_id', 'created_by_user_id', 'updated_by_user_id'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    const TYPE_MATERIAL = 0;
    const TYPE_RAD_CONTRACTORS = 1;
    const TYPE_RAD_SELF = 2;
    const TYPE_SERVICE_OBJECT = 3;
    const TYPE_SERVICE_GENERAL = 4;

    public static function getBudgetList(): array
    {
        return [
            [
                'name' => 'Материалы',
                'type_id' => self::TYPE_MATERIAL,
            ],
            [
                'name' => 'Работы - подрядчики',
                'type_id' => self::TYPE_RAD_CONTRACTORS,
            ],
            [
                'name' => 'Работы - свои силы',
                'type_id' => self::TYPE_RAD_SELF,
            ],
            [
                'name' => 'Накладные расходы объекта',
                'type_id' => self::TYPE_SERVICE_OBJECT,
            ],
            [
                'name' => 'Общие затраты офиса',
                'type_id' => self::TYPE_SERVICE_GENERAL,
            ]
        ];
    }
}
