<?php

namespace App\Models\CashAccount;

use App\Models\CRM\User;
use App\Models\Object\BObject;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashAccount extends Model
{
    use HasUser, SoftDeletes;

    protected $table = 'cash_accounts';

    protected $fillable = [
        'created_by_user_id', 'updated_by_user_id', 'responsible_user_id', 'name', 'start_balance_amount',
        'status_id', 'balance_amount'
    ];

    const STATUS_ACTIVE = 0;
    const STATUS_ARCHIVED = 1;
    const STATUS_DELETED = 2;

    public function sharedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cash_account_user', 'user_id', 'cash_account_id');
    }

    public function objects(): BelongsToMany
    {
        return $this->belongsToMany(BObject::class, 'cash_account_object', 'object_id', 'cash_account_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CashAccountPayment::class, 'cash_account_id');
    }

    public function getStatus(): string
    {
        return $this->getStatuses()[$this->status_id];
    }

    public function getStatusColor(): string
    {
        return $this->getStatusColors()[$this->status_id];
    }

    public function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_ARCHIVED => 'В архиве',
            self::STATUS_DELETED => 'Удален',
        ];
    }

    public function getStatusColors(): array
    {
        return [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_ARCHIVED => 'warning',
            self::STATUS_DELETED => 'danger',
        ];
    }

    public function getBalance(): float
    {
        return $this->balance_amount;
    }
}
