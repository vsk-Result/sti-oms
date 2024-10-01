<?php

namespace App\Models\CashFlow;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class PlanPaymentGroup extends Model implements Audit
{
    use HasUser, HasStatus, Auditable;

    protected $table = 'cash_flow_plan_payments_groups';

    protected $fillable = [
        'created_by_user_id', 'updated_by_user_id', 'name', 'status_id'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(PlanPayment::class, 'group_id');
    }
}
