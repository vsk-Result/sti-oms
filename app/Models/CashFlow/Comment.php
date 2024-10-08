<?php

namespace App\Models\CashFlow;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Comment extends Model implements Audit
{
    use HasUser, HasStatus, Auditable;

    protected $table = 'cash_flow_comments';

    protected $fillable = [
        'created_by_user_id', 'updated_by_user_id', 'text', 'period', 'target_info', 'type_id'
    ];

    const TYPE_RECEIVE = 0;
    const TYPE_PAYMENT = 1;
}
