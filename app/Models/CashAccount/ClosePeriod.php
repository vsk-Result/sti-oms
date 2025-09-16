<?php

namespace App\Models\CashAccount;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClosePeriod extends Model
{
    use HasUser;

    protected $table = 'cash_accounts_close_periods';

    protected $fillable = [
        'cash_account_id', 'created_by_user_id', 'updated_by_user_id', 'period',
        'payments_count', 'payments_amount', 'balance_when_closed',
    ];

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }
}
