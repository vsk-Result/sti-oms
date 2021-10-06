<?php

namespace App\Models;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use App\Traits\HasBank;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statement extends Model
{
    use SoftDeletes, HasUser, HasStatus, HasBank;

    protected $table = 'statements';

    protected $fillable = [
        'bank_id', 'company_id', 'created_by_user_id', 'updated_by_user_id', 'date', 'payments_count',
        'amount_pay', 'amount_receive', 'incoming_balance', 'outgoing_balance', 'file', 'status_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'statement_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->date)->format($format);
    }

    public function getAmountPay(): string
    {
        return number_format($this->amount_pay, 2, '.', ' ');
    }

    public function getAmountReceive(): string
    {
        return number_format($this->amount_receive, 2, '.', ' ');
    }

    public function reCalculateAmountsAndCounts(): void
    {
        $payments = $this->payments;
        $this->update([
            'payments_count' => $payments->count(),
            'amount_pay' => $payments->where('amount', '<', 0)->sum('amount'),
            'amount_receive' => $payments->where('amount', '>=', 0)->sum('amount'),
            'status_id' => $payments->where('status_id', Status::STATUS_BLOCKED)->count() > 0
                ? Status::STATUS_BLOCKED
                : Status::STATUS_ACTIVE,
        ]);
    }

    public function getIncomingBalance(): string
    {
        return number_format($this->incoming_balance, 2, '.', ' ');
    }

    public function getOutgoingBalance(): string
    {
        return number_format($this->outgoing_balance, 2, '.', ' ');
    }
}
