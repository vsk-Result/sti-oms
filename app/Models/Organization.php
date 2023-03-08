<?php

namespace App\Models;

use App\Models\Debt\Debt;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Organization extends Model implements Audit
{
    use SoftDeletes, HasStatus, Auditable;

    protected $table = 'organizations';

    protected $fillable = ['company_id', 'name', 'inn', 'kpp', 'status_id', 'nds_status_id'];

    const NDS_STATUS_AUTO = 0;
    const NDS_STATUS_ALWAYS = 1;
    const NDS_STATUS_NEVER = 2;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function paymentsSend(): HasMany
    {
        return $this->hasMany(Payment::class, 'organization_sender_id');
    }

    public function paymentsReceive(): HasMany
    {
        return $this->hasMany(Payment::class, 'organization_receiver_id');
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class, 'organization_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'organization_id');
    }

    public function bankGuarantees(): HasMany
    {
        return $this->hasMany(BankGuarantee::class, 'organization_id');
    }

    public function guarantees(): HasMany
    {
        return $this->hasMany(Guarantee::class, 'organization_id');
    }

    public function objects(): BelongsToMany
    {
        return $this->belongsToMany(BObject::class, 'object_customer', 'object_id', 'customer_id');
    }

    public static function getNDSStatuses(): array
    {
        return [
            self::NDS_STATUS_AUTO => 'Автоматический',
            self::NDS_STATUS_ALWAYS => 'Всегда',
            self::NDS_STATUS_NEVER => 'Никогда'
        ];
    }

    public function isNDSAuto(): bool
    {
        return $this->nds_status_id === self::NDS_STATUS_AUTO;
    }

    public function isNDSAlways(): bool
    {
        return $this->nds_status_id === self::NDS_STATUS_ALWAYS;
    }

    public function isNDSNever(): bool
    {
        return $this->nds_status_id === self::NDS_STATUS_NEVER;
    }
}
