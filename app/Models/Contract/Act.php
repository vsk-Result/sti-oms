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

class Act extends Model implements Audit
{
    use SoftDeletes, HasUser, HasStatus, Auditable;

    protected $table = 'acts';

    protected $fillable = [
        'contract_id', 'company_id', 'object_id', 'created_by_user_id', 'updated_by_user_id', 'date',
        'amount', 'amount_avans', 'amount_deposit', 'amount_need_paid', 'description', 'status_id'
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

    public function getAmount(): string
    {
        return number_format($this->amount, 2, '.', ' ');
    }

    public function getAvansAmount(): string
    {
        return number_format($this->amount_avans, 2, '.', ' ');
    }

    public function getDepositAmount(): string
    {
        return number_format($this->amount_deposit, 2, '.', ' ');
    }

    public function getNeedPaidAmount(): string
    {
        return number_format($this->amount_need_paid, 2, '.', ' ');
    }

    public function getPaidAmount($formatted = true): string
    {
        $amount = $this->payments->sum('amount');
        return $formatted ? number_format($amount, 2, '.', ' ') : $amount;
    }

    public function getLeftPaidAmount($formatted = true): string
    {
        $amount = $this->amount_need_paid - $this->payments->sum('amount');
        return $formatted ? number_format($amount, 2, '.', ' ') : $amount;
    }
}
