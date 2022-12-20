<?php

namespace App\Models;

use App\Traits\HasBank;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class Loan extends Model implements Audit
{
    use SoftDeletes, HasUser, HasStatus, HasBank, Auditable;

    protected $table = 'loans';

    protected $fillable = [
        'type_id', 'bank_id', 'company_id', 'created_by_user_id', 'updated_by_user_id',
        'start_date', 'end_date', 'name', 'percent', 'amount', 'total_amount', 'status_id', 'description',
        'organization_id', 'search_name'
    ];

    const TYPE_CREDIT = 0;
    const TYPE_LOAN = 1;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function notifyTags(): HasMany
    {
        return $this->hasMany(LoanNotifyTag::class, 'loan_id');
    }

    public function historyPayments(): HasMany
    {
        return $this->hasMany(LoanHistory::class, 'loan_id');
    }

    public function getStartDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->start_date ? Carbon::parse($this->start_date)->format($format) : '';
    }

    public function getEndDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->end_date ? Carbon::parse($this->end_date)->format($format) : '';
    }

    public static function getTypes(): array
    {
        return [
            self::TYPE_CREDIT => 'Кредит',
            self::TYPE_LOAN => 'Займ',
        ];
    }

    public function getType(): string
    {
        return self::getTypes()[$this->type_id];
    }

    public function isCredit(): bool
    {
        return $this->type_id === self::TYPE_CREDIT;
    }
}
