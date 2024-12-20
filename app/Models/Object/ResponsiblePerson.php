<?php

namespace App\Models\Object;

use App\Traits\HasStatus;
use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponsiblePerson extends Model
{
    use HasUser, HasStatus;

    protected $table = 'object_responsible_persons';

    protected $fillable = ['object_id', 'position_id', 'fullname', 'email', 'phone', 'created_by_user_id', 'updated_by_user_id', 'status_id'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }
}
