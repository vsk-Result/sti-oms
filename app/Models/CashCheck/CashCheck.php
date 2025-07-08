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
        'crm_user_id', 'crm_cost_id', 'period', 'status_id', 'email_send_status_id'
    ];

    const STATUS_UNCKECKED = 0;
    const STATUS_CHECKING = 1;
    const STATUS_CHECKED = 2;

    const EMAIL_SEND_STATUS_NOT_SEND = 0;
    const EMAIL_SEND_STATUS_SEND_WITH_ERROR = 1;
    const EMAIL_SEND_STATUS_SEND = 2;

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

    public function getEmailStatus(): string
    {
        return $this->getEmailStatuses()[$this->email_send_status_id];
    }

    public function getStatus(): string
    {
        return self::getStatuses()[$this->status_id];
    }

    public function getStatusColor(): string
    {
        return $this->getStatusColors()[$this->status_id];
    }

    public function getEmailStatusColor(): string
    {
        return $this->getEmailStatusColors()[$this->email_send_status_id];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_UNCKECKED => 'Не проверен',
            self::STATUS_CHECKING => 'Проверяется',
            self::STATUS_CHECKED => 'Проверен',
        ];
    }

    public function getEmailStatuses(): array
    {
        return [
            self::EMAIL_SEND_STATUS_NOT_SEND => 'Не отправлен',
            self::EMAIL_SEND_STATUS_SEND_WITH_ERROR => 'Отправлено с ошибкой',
            self::EMAIL_SEND_STATUS_SEND => 'Отправлено'
        ];
    }

    public function getStatusColors(): array
    {
        return [
            self::STATUS_UNCKECKED => 'danger',
            self::STATUS_CHECKING => 'warning',
            self::STATUS_CHECKED => 'success',
        ];
    }

    public function getEmailStatusColors(): array
    {
        return [
            self::EMAIL_SEND_STATUS_NOT_SEND => 'danger',
            self::EMAIL_SEND_STATUS_SEND_WITH_ERROR => 'warning',
            self::EMAIL_SEND_STATUS_SEND => 'success'
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
        if (auth()->user()->hasRole('super-admin')) {
            return $query;
        }

        return $query->whereHas('managers', function ($q) use ($managerId) {
            return $q->where('manager_id', $managerId);
        });
    }

    public function scopeUnchecked($query)
    {
        return $query->whereIn('status_id', [self::STATUS_UNCKECKED, self::STATUS_CHECKING]);
    }

    public function scopeChecked($query)
    {
        return $query->whereIn('status_id', [self::STATUS_CHECKED]);
    }

    public function scopeNotSended($query)
    {
        return $query->whereIn('email_send_status_id', [self::EMAIL_SEND_STATUS_NOT_SEND, self::EMAIL_SEND_STATUS_SEND_WITH_ERROR]);
    }

    public function scopeReadyToSend($query)
    {
        return $query->where('updated_at', '<=', now()->subHour());
    }

    public function isSended(): bool
    {
        return $this->email_send_status_id === self::EMAIL_SEND_STATUS_SEND;
    }

    public static function getFormattedPeriodGeneral($period): string
    {
        $period = Carbon::parse($period . '-01')->format('F Y');
        $period = str_replace('January', 'Январь', $period);
        $period = str_replace('February', 'Февраль', $period);
        $period = str_replace('March', 'Март', $period);
        $period = str_replace('April', 'Апрель', $period);
        $period = str_replace('May', 'Май', $period);
        $period = str_replace('June', 'Июнь', $period);
        $period = str_replace('July', 'Июль', $period);
        $period = str_replace('August', 'Август', $period);
        $period = str_replace('September', 'Сентябрь', $period);
        $period = str_replace('October', 'Октябрь', $period);
        $period = str_replace('November', 'Ноябрь', $period);
        $period = str_replace('December', 'Декабрь', $period);

        return $period;
    }

    public function getFormattedPeriod(): string
    {
        return self::getFormattedPeriodGeneral($this->period);
    }
}
