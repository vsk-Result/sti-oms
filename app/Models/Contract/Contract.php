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

class Contract extends Model
{
    use SoftDeletes, HasUser, HasStatus;

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
        return Carbon::parse($this->start_date)->format($format);
    }

    public function getEndDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->end_date)->format($format);
    }

    public function getAmount(): string
    {
        return number_format($this->amount, 2, '.', ' ');
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
            self::AMOUNT_TYPE_MAIN => 'Основной',
            self::AMOUNT_TYPE_ADDITIONAL => 'Дополнительный',
        ];
    }
}
