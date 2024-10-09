<?php

namespace App\Models\CRM;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvansImport extends Model
{
    protected $table = 'avans_imports';

    protected $connection = 'mysql_crm';

    public function items(): HasMany
    {
        return $this->hasMany(AvansImportItem::class, 'import_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getDate()
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }

    public function getItemsSum()
    {
        $sum = 0;

        foreach ($this->items as $item) {

            if (!$item->avans) {
                $item->delete();
                continue;
            }
            $sum += $item->avans->value;
        }

        return $sum;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            if ($item->avans) {
                $total += $item->avans->value;
            }
        }

        return $total;
    }
}
