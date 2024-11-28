<?php

namespace App\Models\CashCheck;

use App\Models\CRM\Cost;
use App\Models\CRM\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class CashCheck extends Model implements Audit
{
    use Auditable;

    protected $table = 'crm_cash_checks';

    protected $fillable = [
        'crm_user_id', 'crm_cost_id', 'period', 'status_id'
    ];

    const STATUS_UNCKECKED = 0;
    const STATUS_CHECKING = 1;
    const STATUS_CHECKED = 2;

    public function crmUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'crm_user_id');
    }

    public function crmCost(): BelongsTo
    {
        return $this->belongsTo(Cost::class, 'crm_cost_id');
    }

    public function managers(): HasMany
    {
        return $this->hasMany(Manager::class, 'check_id');
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
            self::STATUS_UNCKECKED => 'Не проверен',
            self::STATUS_CHECKING => 'Проверяется',
            self::STATUS_CHECKED => 'Проверен'
        ];
    }

    public function getStatusColors(): array
    {
        return [
            self::STATUS_UNCKECKED => 'danger',
            self::STATUS_CHECKING => 'warning',
            self::STATUS_CHECKED => 'success'
        ];
    }

    public function isChecked(): bool
    {
        return $this->status_id === self::STATUS_CHECKED;
    }

    public function isChecking(): bool
    {
        return $this->status_id === self::STATUS_CHECKING;
    }

    public function scopeForManager($query, int $managerId)
    {
        return $query->whereHas('managers', function ($q) use ($managerId) {
            return $q->where('manager_id', $managerId);
        });
    }

    public function scopeUnchecked($query)
    {
        return $query->whereIn('status_id', [self::STATUS_UNCKECKED, self::STATUS_CHECKING]);
    }

    public function getFormattedPeriod(): string
    {
        return Carbon::parse($this->period . '-01')->format('F Y');
    }
}
