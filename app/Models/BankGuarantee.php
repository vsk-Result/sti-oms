<?php

namespace App\Models;

use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Traits\HasBank;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class BankGuarantee extends Model implements Audit, HasMedia
{
    use SoftDeletes, HasUser, HasStatus, HasBank, InteractsWithMedia, Auditable;

    protected $table = 'bank_guarantees';

    protected $fillable = [
        'company_id', 'bank_id', 'object_id', 'created_by_user_id', 'updated_by_user_id', 'start_date',
        'end_date', 'amount', 'start_date_deposit', 'end_date_deposit', 'amount_deposit', 'target', 'status_id',
        'contract_id', 'organization_id', 'number', 'commission', 'currency', 'currency_rate'
    ];

    private function getStatusesList(): array
    {
        return [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'В архиве',
            Status::STATUS_DELETED => 'Удален'
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
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

    public function getStartDateDepositFormatted(string $format = 'd/m/Y'): string
    {
        return $this->start_date_deposit ? Carbon::parse($this->start_date_deposit)->format($format) : '';
    }

    public function getEndDateDepositFormatted(string $format = 'd/m/Y'): string
    {
        return $this->end_date_deposit ? Carbon::parse($this->end_date_deposit)->format($format) : '';
    }

    public static function getTargetsList(): array
    {
       return [
          '-',
          'Обеспечение аванса',
          'Обеспечение контракта',
          'Обеспечение депозита',
       ] ;
    }

    public function getAvansesLeftAmount(): string
    {
        $avansesReceived = $this->contract?->avansesReceived->sum('amount');
        $avansesWithheld = $this->contract?->acts->sum('amount_avans');

        return $avansesReceived - $avansesWithheld;
    }
}
