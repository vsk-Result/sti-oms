<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class LoanNotifyTag extends Model implements Audit
{
    use HasUser, Auditable;

    protected $table = 'loan_notify_tags';

    protected $fillable = [
        'loan_id', 'created_by_user_id', 'updated_by_user_id', 'tag'
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
