<?php

namespace App\Models\Contract;

use App\Models\BankGuarantee;
use App\Models\Company;
use App\Models\Guarantee;
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
        'name', 'start_date', 'end_date', 'amount', 'amount_type_id', 'description', 'stage_id', 'status_id',
        'currency', 'currency_rate', 'params'
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

    public function bankGuarantees(): HasMany
    {
        return $this->hasMany(BankGuarantee::class, 'contract_id');
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class, 'contract_id');
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
        return $this->start_date ? Carbon::parse($this->start_date)->format($format) : '...';
    }

    public function getEndDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format($format) : '...';
    }

    public function isMain(): string
    {
        return $this->type_id === self::TYPE_MAIN;
    }

    public function isMainAmount(): string
    {
        return $this->amount_type_id === self::AMOUNT_TYPE_MAIN;
    }

    public function getAmount(string $currency = null): string|array
    {
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $this->amount : 0;

        if ($this->isMain()) {

            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[7])) {
                return json_decode($this->params)[7] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount = $subContract->isMainAmount()
                    ? $subContract->amount
                    : $amount + $subContract->amount;
            }
        }

        return $amount;
    }

    public function getAvansesAmount(string $currency = null): string|array
    {
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $this->avanses->sum('amount') : 0;
        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && array_key_exists(8, json_decode($this->params) ?? [])) {
                return json_decode($this->params)[8] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->avanses->sum('amount');
            }
        }

        return $amount;
    }

    public function getAvansesReceivedAmount(string $currency = null): string|array
    {
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $this->avansesReceived->sum('amount') : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[9])) {
                return json_decode($this->params)[9] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->avansesReceived->sum('amount');
            }
        }

        return $amount;
    }

    public function getAvansesLeftAmount(string $currency = null): string|array
    {
        $amount = $this->avanses->sum('amount') - $this->avansesReceived->sum('amount');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && array_key_exists(10, json_decode($this->params) ?? [])) {
                return json_decode($this->params)[10] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->avanses->sum('amount') - $subContract->avansesReceived->sum('amount');
            }
        }

        return $amount;
    }

    public function getActsAmount(string $currency = null): string|array
    {
        $amount = $this->acts->sum('amount');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && array_key_exists(12, json_decode($this->params) ?? [])) {
                return json_decode($this->params)[12] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount');
            }
        }

        return $amount;
    }

    public function getActsAvasesAmount(string $currency = null): string|array
    {
        $amount = $this->acts->sum('amount_avans');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[13])) {
                return json_decode($this->params)[13] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount_avans');
            }
        }

        return $amount;
    }

    public function getActsDepositesAmount(string $currency = null): string|array
    {
        $amount = $this->acts->sum('amount_deposit');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[14])) {
                return json_decode($this->params)[14] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount_deposit');
            }
        }

        return $amount;
    }

    public function getActsNeedPaidAmount(string $currency = null): string|array
    {
        $amount = $this->acts->sum('amount_need_paid');
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[15])) {
                return json_decode($this->params)[15] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount_need_paid');
            }
        }

        return $amount;
    }

    public function getActsPaidAmount(string $currency = null): string|array
    {
        $amount = 0;
        foreach ($this->acts as $act) {
            $amount += $act->payments->sum('amount');
        }
        $amount = ($currency === null || ($currency !== null && $this->currency === $currency)) ? $amount : 0;

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[16])) {
                return json_decode($this->params)[16] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                foreach ($subContract->acts as $act) {
                    $amount += $act->payments->sum('amount');
                }
            }
        }

        return $amount;
    }

    public function getActsLeftPaidAmount(string $currency = null): string|array
    {
        $paid = 0;
        foreach ($this->acts as $act) {
            $paid += $act->payments->sum('amount');
        }

        if ($this->isMain()) {
            if ($this->object_id === 5 && $currency === 'RUB' && isset(json_decode($this->params)[17])) {
                return json_decode($this->params)[17] ?? 0;
            }

            foreach ($this->children->where('currency', $currency) as $subContract) {
                foreach ($subContract->acts as $act) {
                    $paid += $act->payments->sum('amount');
                }
            }
        }

        $amount = $this->acts->sum('amount_need_paid');

        if ($this->isMain()) {
            foreach ($this->children->where('currency', $currency) as $subContract) {
                $amount += $subContract->acts->sum('amount_need_paid');
            }
        }

        if ($this->object_id === 5 && $currency === 'RUB' && ($amount - $paid) < 0) {
            return 0;
        }

        return $amount - $paid;
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
//            self::TYPE_ADDITIONAL => 'Доп. соглашение ',
//            self::TYPE_PHASE => '',
//            self::TYPE_PRELIMINARY => 'Предв. договор ',
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
