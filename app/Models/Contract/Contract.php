<?php

namespace App\Models\Contract;

use App\Models\Company;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Contract extends Model implements HasMedia, Audit
{
    use SoftDeletes, HasUser, HasStatus, InteractsWithMedia, Auditable;

    protected $table = 'contracts';

    protected $fillable = [
        'parent_id', 'type_id', 'company_id', 'object_id', 'created_by_user_id', 'updated_by_user_id',
        'name', 'start_date', 'end_date', 'amount', 'amount_type_id', 'description', 'stage_id', 'status_id'
    ];

    const TYPE_MAIN = 0;
    const TYPE_ADDITIONAL = 1;
    const TYPE_PHASE = 2;
    const TYPE_PRELIMINARY = 3;

    const AMOUNT_TYPE_MAIN = 0;
    const AMOUNT_TYPE_ADDITIONAL = 1;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function acts(): HasMany
    {
        return $this->hasMany(Act::class, 'contract_id');
    }

    public function avanses(): HasMany
    {
        return $this->hasMany(ContractAvans::class, 'contract_id');
    }

    public function avansesReceived(): HasMany
    {
        return $this->hasMany(ContractReceivedAvans::class, 'contract_id');
    }

    public function actPayments(): HasMany
    {
        return $this->hasMany(ActPayment::class, 'contract_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function getStartDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format($format) : '';
    }

    public function getEndDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format($format) : '';
    }

    public function getAmount(): string
    {
        $amount = $this->amount;

        if ($this->isMain()) {
            $subContract = $this->children->sortByDesc('id')->first();
            if ($subContract) {
                $amount = $subContract->isMainAmount()
                    ? $subContract->amount
                    : $amount + $subContract->amount;
            }
        }

        return number_format($amount, 2, '.', ' ');
    }

    public function isMain(): string
    {
        return $this->type_id === self::TYPE_MAIN;
    }

    public function isMainAmount(): string
    {
        return $this->amount_type_id === self::AMOUNT_TYPE_MAIN;
    }

    public function getAvansesAmount(): string
    {
        return number_format($this->avanses->sum('amount'), 2, '.', ' ');
    }

    public function getAvansesReceivedAmount(): string
    {
        return number_format($this->avansesReceived->sum('amount'), 2, '.', ' ');
    }

    public function getAvansesLeftAmount(): string
    {
        return number_format($this->avanses->sum('amount') - $this->avansesReceived->sum('amount'), 2, '.', ' ');
    }

    public function getActsAmount(): string
    {
        return number_format($this->acts->sum('amount'), 2, '.', ' ');
    }

    public function getActsAvasesAmount(): string
    {
        return number_format($this->acts->sum('amount_avans'), 2, '.', ' ');
    }

    public function getActsDepositesAmount(): string
    {
        return number_format($this->acts->sum('amount_deposit'), 2, '.', ' ');
    }

    public function getActsNeedPaidAmount(): string
    {
        return number_format($this->acts->sum('amount_need_paid'), 2, '.', ' ');
    }

    public function getActsPaidAmount(): string
    {
        $paid = 0;
        foreach ($this->acts as $act) {
            $paid += $act->payments->sum('amount');
        }

        return number_format($paid, 2, '.', ' ');
    }

    public function getActsLeftPaidAmount(): string
    {
        $paid = 0;
        foreach ($this->acts as $act) {
            $paid += $act->payments->sum('amount');
        }

        return number_format($this->acts->sum('amount_need_paid') - $paid, 2, '.', ' ');
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_MAIN => 'Основной',
            self::TYPE_ADDITIONAL => 'Дополнительный',
            self::TYPE_PHASE => 'Фаза',
            self::TYPE_PRELIMINARY => 'Предварительный'
        ];
    }

    public static function getAmountTypes(): array
    {
        return [
            self::AMOUNT_TYPE_MAIN => 'Основная',
            self::AMOUNT_TYPE_ADDITIONAL => 'Дополнительная',
        ];
    }

    public function getName(): string
    {
        $prefix = match ($this->type_id) {
            self::TYPE_ADDITIONAL => 'Доп. соглашение ',
            self::TYPE_PHASE => 'Фаза ',
            self::TYPE_PRELIMINARY => 'Предв. договор ',
            default => '',
        };

        return $prefix . $this->name;
    }

    public function getType(): string
    {
        return self::getTypes()[$this->type_id];
    }

    public function getAmountType(): string
    {
        return self::getAmountTypes()[$this->amount_type_id];
    }
}
