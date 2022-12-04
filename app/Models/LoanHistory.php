<?php

namespace App\Models;

use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class LoanHistory extends Model implements Audit
{
    use HasUser, Auditable, SoftDeletes;

    protected $table = 'loans_history';

    protected $fillable = [
        'loan_id', 'created_by_user_id', 'updated_by_user_id', 'date', 'refund_date', 'planned_refund_date', 'amount', 'percent',
        'description', 'status_id'
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->date ? Carbon::parse($this->date)->format($format) : '';
    }

    public function getRefundDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->refund_date ? Carbon::parse($this->refund_date)->format($format) : '';
    }

    public function getPlannedRefundDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->planned_refund_date ? Carbon::parse($this->planned_refund_date)->format($format) : '';
    }
}
