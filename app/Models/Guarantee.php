<?php

namespace App\Models;

use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Guarantee extends Model implements Audit, HasMedia
{
    use SoftDeletes, HasUser, HasStatus, InteractsWithMedia, Auditable;

    protected $table = 'guarantees';

    protected $fillable = [
        'company_id', 'object_id', 'created_by_user_id', 'updated_by_user_id', 'contract_id', 'organization_id',
        'amount', 'fact_amount', 'has_final_act', 'has_bank_guarantee', 'state', 'conditions', 'status_id', 'currency',
        'amount_payments'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(GuaranteePayment::class, 'guarantee_id');
    }

    public function getBankGuaranteeState(): string
    {
        return $this->has_bank_guarantee ? 'Есть' : 'Нет';
    }

    public function getFinalActState(): string
    {
        if (is_null($this->has_final_act)) {
            return '';
        }
        return $this->has_final_act ? 'Есть' : 'Нет';
    }

    public function getFactAmount()
    {
        return $this->fact_amount - $this->amount_payments;
    }

    public function updatePayments()
    {
        $this->update([
            'amount_payments' => $this->payments->sum('amount')
        ]);
    }

    public function getLastPaymentDate($formatted = false, $toExcel = false): string
    {
        $lastPayment = $this->payments()->orderBy('date', 'DESC')->first();

        if (!$lastPayment) {
            return '';
        }

        if (empty($lastPayment->date)) {
            return $lastPayment->description ?? '';
        }

        if ($toExcel) {
            return Date::dateTimeToExcel(Carbon::parse($lastPayment->date));
        }

        return $formatted ? Carbon::parse($lastPayment->date)->format('d.m.Y') : $lastPayment->date;
    }
}
