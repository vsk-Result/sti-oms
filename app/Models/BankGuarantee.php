<?php

namespace App\Models;

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

class BankGuarantee extends Model implements HasMedia
{
    use SoftDeletes, HasUser, HasStatus, HasBank, InteractsWithMedia;

    protected $table = 'bank_guarantees';

    protected $fillable = [
        'company_id', 'bank_id', 'object_id', 'created_by_user_id', 'updated_by_user_id', 'start_date',
        'end_date', 'amount', 'start_date_deposit', 'end_date_deposit', 'amount_deposit', 'target', 'status_id'
    ];

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
        return number_format($this->amount, 2, '.', ' ');
    }

    public function getStartDateDepositFormatted(string $format = 'd/m/Y'): string
    {
        return $this->start_date_deposit ? Carbon::parse($this->start_date_deposit)->format($format) : '';
    }

    public function getEndDateDepositFormatted(string $format = 'd/m/Y'): string
    {
        return $this->end_date_deposit ? Carbon::parse($this->end_date_deposit)->format($format) : '';
    }

    public function getAmountDeposit(): string
    {
        return number_format($this->amount_deposit, 2, '.', ' ');
    }

    public static function getTargetsList()
    {
       return [
          '-',
          'Обеспечение аванса',
          'Обеспечение контракта',
          'Обеспечение депозита',
       ] ;
    }
}
