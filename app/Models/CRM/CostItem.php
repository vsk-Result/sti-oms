<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostItem extends Model
{
    protected $table = 'cost_items';

    protected $connection = 'mysql_crm';

    public function object(): BelongsTo
    {
        return $this->belongsTo(CObject::class, 'object_id');
    }

    public function avans(): BelongsTo
    {
        return $this->belongsTo(Avans::class, 'avans_id');
    }

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class, 'kost_id');
    }

    public function kostCat(): BelongsTo
    {
        return $this->belongsTo(KostCategory::class, 'kost_cat_id');
    }

    public function getKostCode()
    {
        if ($this->kost) {
            return $this->kost->category_id . '.' . $this->kost->code;
        } else if ($this->kostCat) {
            return $this->kost_cat_id;
        } else {
            return '';
        }
    }
}
