<?php

namespace App\Models\Agreement;

use App\Models\Company;
use App\Models\Object\BObject;
use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Agreement extends Model implements HasMedia
{
    use SoftDeletes, HasStatus, HasUser, InteractsWithMedia;

    protected $table = 'agreements';

    protected $fillable = ['company_id', 'object_id', 'object_worktype_id', 'name', 'start_date', 'end_date', 'amount'];

    protected $dates = ['start_date', 'end_date'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public function getObjectId(): string
    {
        return "$this->object_id::$this->object_worktype_id";
    }
}
