<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avans extends Model
{
    protected $table = 'avanses';

    protected $connection = 'mysql_crm';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'e_id');
    }
}
