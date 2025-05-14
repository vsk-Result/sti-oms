<?php

namespace App\Models\Object;

use App\Models\Organization;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class CashFlowPayment extends Model implements Audit
{
    use HasUser, Auditable, HasStatus;

    protected $table = 'object_cash_flow_payments';

    protected $fillable = ['object_id', 'category_id', 'organization_id', 'date', 'amount', 'created_by_user_id', 'updated_by_user_id', 'status_id'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    const CATEGORY_RAD = 0;
    const CATEGORY_MATERIAL = 1;
    const CATEGORY_MATERIAL_FIX = 2;
    const CATEGORY_MATERIAL_FLOAT = 3;
    const CATEGORY_SERVICE = 4;

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_RAD => 'Работы',
            self::CATEGORY_MATERIAL => 'Материалы',
            self::CATEGORY_MATERIAL_FIX => 'Материалы - фиксированная часть',
            self::CATEGORY_MATERIAL_FLOAT => 'Материалы - изменяемая часть',
            self::CATEGORY_SERVICE => 'Накладные/Услуги',
        ];
    }

    public function getCategory(): string
    {
        return $this->getCategories()[$this->category_id];
    }
}
