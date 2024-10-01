<?php

namespace App\Models\CashFlow;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class PlanPaymentEntry extends Model implements Audit
{
    use HasUser, HasStatus, Auditable;

    protected $table = 'cash_flow_plan_payment_entries';

    protected $fillable = [
        'created_by_user_id', 'updated_by_user_id', 'payment_id', 'date', 'amount', 'status_id'
    ];

    public function planPayment(): BelongsTo
    {
        return $this->belongsTo(PlanPayment::class, 'payment_id');
    }
}
