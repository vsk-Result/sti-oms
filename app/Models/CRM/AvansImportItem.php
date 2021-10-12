<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class AvansImportItem extends Model
{
    protected $table = 'avans_import_items';

    protected $connection = 'mysql_crm';

    public function avans()
    {
        return $this->belongsTo(Avans::class, 'avans_id');
    }
}
