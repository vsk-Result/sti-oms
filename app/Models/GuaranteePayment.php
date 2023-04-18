<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class GuaranteePayment extends Model implements Audit
{
    use SoftDeletes, HasUser, HasStatus, Auditable;

    protected $table = 'guarantee_payments';

    protected $fillable = [
        'contract_id', 'guarantee_id', 'company_id', 'object_id', 'created_by_user_id', 'organization_id',
        'updated_by_user_id', 'date', 'amount', 'status_id', 'currency'
    ];

    public function guarantee(): BelongsTo
    {
        return $this->belongsTo(Guarantee::class, 'guarantee_id');
    }

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

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function getDateFormatted(string $format = 'd/m/Y'): string
    {
        return $this->date ? Carbon::parse($this->date)->format($format) : '';
    }
}
