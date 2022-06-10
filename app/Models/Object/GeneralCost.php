<?php

namespace App\Models\Object;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralCost extends Model
{
    protected $table = 'object_general_costs';

    protected $fillable = ['object_id', 'amount', 'is_pinned'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }
}
