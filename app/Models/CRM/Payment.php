<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $connection = 'mysql_crm';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'u_id');
    }
}
