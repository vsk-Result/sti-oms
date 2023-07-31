<?php

namespace App\Models\Debt;

use App\Models\Object\BObject;
use App\Models\Organization;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;

class DebtManual extends Model implements Audit
{
    use SoftDeletes, HasUser, HasStatus, Auditable;

    protected $table = 'debt_manuals';

    protected $fillable = [
        'type_id', 'object_id', 'object_worktype_id', 'created_by_user_id', 'updated_by_user_id',
        'organization_id', 'amount', 'status_id', 'avans', 'guarantee'];

    const TYPE_CONTRACTOR = 0;
    const TYPE_PROVIDER = 1;

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }
}
