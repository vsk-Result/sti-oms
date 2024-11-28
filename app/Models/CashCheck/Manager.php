<?php

namespace App\Models\CashCheck;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Manager extends Model implements Audit
{
    use Auditable;

    protected $table = 'crm_cash_check_managers';

    protected $fillable = [
        'check_id', 'manager_id', 'status_id'
    ];

    const STATUS_UNCKECKED = 0;
    const STATUS_CHECKED = 1;

    public function check(): BelongsTo
    {
        return $this->belongsTo(CashCheck::class, 'check_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function getStatuses(): array
    {
        return [
            self::STATUS_UNCKECKED => 'Не проверен',
            self::STATUS_CHECKED => 'Проверен'
        ];
    }

    public function isChecked(): bool
    {
        return $this->status_id === self::STATUS_CHECKED;
    }

    public function isUser(): bool
    {
        return $this->manager_id === auth()->id();
    }
}
