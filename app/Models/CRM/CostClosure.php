<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostClosure extends Model
{
    protected $table = 'cost_closures';
    public $timestamps = false;

    protected $connection = 'mysql_crm';

    public function cost(): BelongsTo
    {
        return $this->belongsTo(Cost::class, 'cost_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
