<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvansImport extends Model
{
    protected $table = 'avans_imports';

    protected $connection = 'mysql_crm';

    public function items(): HasMany
    {
        return $this->hasMany(AvansImportItem::class, 'import_id');
    }
}
