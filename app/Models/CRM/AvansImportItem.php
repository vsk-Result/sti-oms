<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvansImportItem extends Model
{
    protected $table = 'avans_import_items';

    protected $connection = 'mysql_crm';

    public function avans(): BelongsTo
    {
        return $this->belongsTo(Avans::class, 'avans_id');
    }
}
