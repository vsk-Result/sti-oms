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
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Act extends Model implements Audit, HasMedia
{
    use SoftDeletes, HasUser, HasStatus, Auditable, InteractsWithMedia;

    protected $table = 'acts';

    protected $fillable = [
        'contract_id', 'company_id', 'object_id', 'created_by_user_id', 'updated_by_user_id', 'date',
        'amount', 'amount_avans', 'amount_deposit', 'amount_need_paid', 'description', 'status_id',
        'currency', 'currency_rate', 'number', 'planned_payment_date'
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ActPayment::class, 'act_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->date)->format($format);
    }

    public function getPlannedPaymentDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->planned_payment_date ? Carbon::parse($this->planned_payment_date)->format($format) : '';
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getAvansAmount(): string
    {
        return $this->amount_avans;
    }

    public function getDepositAmount(): string
    {
        return $this->amount_deposit;
    }

    public function getNeedPaidAmount(): string
    {
        return $this->amount_need_paid;
    }

    public function getPaidAmount(): string
    {
        return $this->payments->sum('amount');
    }

    public function getLeftPaidAmount(): string
    {
        return $this->amount_need_paid - $this->payments->sum('amount');
    }
}
