<?php

namespace App\Models\Contract;

use App\Models\Company;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractReceivedAvans extends Model
{
    use SoftDeletes, HasUser, HasStatus;

    protected $table = 'contract_avanses';

    protected $fillable = [
        'contract_id', 'company_id', 'object_id', 'created_by_user_id', 'updated_by_user_id', 'date', 'amount', 'status_id'
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

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return Carbon::parse($this->date)->format($format);
    }

    public function getAmount(): string
    {
        return number_format($this->amount, 2, '.', ' ');
    }
}
