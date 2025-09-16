<?php

namespace App\Models\CashAccount;

use App\Models\KostCode;
use App\Models\Object\WorkType;
use App\Models\User;
use App\Models\Object\BObject;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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

    public function closePeriods(): HasMany
    {
        return $this->hasMany(ClosePeriod::class, 'cash_account_id');
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


    }public function getBalanceWithTransferApprove(): float
    {
        return $this->balance_amount + $this->payments()->where('status_id', CashAccountPayment::STATUS_WAITING)->sum('amount');
    }

    public function getObjects(): array
    {
        $result = [];
        $workTypes = WorkType::getWorkTypes();
        $objects = $this->objects;

        if ($objects->count() === 0) {
            $objects = BObject::active(['27.1', '27.3', '288', '346'])->orderBy('code', 'desc')->get();
        }

        foreach ($objects as $object) {
            if ($object->isWithoutWorktype()) {
                $result[$object->id . '::' . null] = $object->getName();
            } else {
                $result[$object->id . '::' . null] = $object->getName();
                foreach ($workTypes as $workType) {
                    $result[$object->id . '::' . $workType['id']] = $object->code . '.' . $workType['code'];
                }
            }
        }

        return $result;
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status_id', [self::STATUS_ACTIVE]);
    }

    public function getPopularPaymentCodes(): array
    {
        $codes = [];
        $topCodes = $this->payments()->select('code', DB::raw('count(*) as total'))
            ->groupBy('code')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'code');

        foreach ($topCodes as $code => $count) {
            $codes[$code] = KostCode::getTitleByCode($code);
        }

        return $codes;
    }

    public function isCurrentResponsible(): bool
    {
        return auth()->user()->hasRole('super-admin')
            || auth()->id() === $this->responsible_user_id
            || in_array(auth()->id(), $this->sharedUsers->pluck('id')->toArray());
    }

    public function getName(): string
    {
        return $this->name . ' | ' . $this->responsible?->name ?? '-';
    }
}
