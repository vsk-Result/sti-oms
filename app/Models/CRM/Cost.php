<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cost extends Model
{
    protected $table = 'costs';

    protected $connection = 'mysql_crm';

    public function items(): HasMany
    {
        return $this->hasMany(CostItem::class, 'cost_id');
    }
}
