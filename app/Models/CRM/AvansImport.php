<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class AvansImport extends Model
{
    protected $table = 'avans_imports';

    protected $connection = 'mysql_crm';

    public function items()
    {
        return $this->hasMany(AvansImportItem::class, 'import_id');
    }
}
