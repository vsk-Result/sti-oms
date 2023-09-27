<?php

namespace App\Models;

use App\Models\Object\BObject;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as Audit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Writeoff extends Model implements Audit, HasMedia
{
    use SoftDeletes, HasUser, HasStatus, Auditable, InteractsWithMedia;

    protected $table = 'writeoffs';

    protected $fillable = [
        'company_id', 'object_id', 'crm_employee_uid', 'created_by_user_id',
        'updated_by_user_id', 'description', 'date', 'amount', 'status_id'
    ];

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
        return empty($this->date) ? '' : Carbon::parse($this->date)->format($format);
    }

    public function getAmount(): string
    {
        return number_format($this->amount, 2, '.', ' ');
    }

    public function getAmountWithoutNDS(): string
    {
        return number_format($this->amount_without_nds, 2, '.', ' ');
    }
}
