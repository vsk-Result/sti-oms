<?php

namespace App\Models\Object;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferService extends Model
{
    protected $table = 'object_transfer_service';

    protected $fillable = ['object_id', 'amount'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }
}
