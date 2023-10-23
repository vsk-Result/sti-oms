<?php

namespace App\Models\Object;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralCost extends Model
{
    protected $table = 'object_general_costs';

    protected $fillable = ['object_id', 'amount', 'is_pinned'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(BObject::class, 'object_id');
    }

    public static function getObjectCodesForGeneralCosts(): array
    {
        // исключили 343 по письму от Оксаны 30 мая 2023
        return [
            '257', '268', '288', '292', '296', '298', '303', '304', '305', '308', '309', '317', '321', '322',
            '323', '325', '327', '330', '332', '333', '334', '335', '338', '339', '341', '342',
            '344', '346', '349', '350', '352', '353', '358', '359', '360', '361', '363', '364'
        ];
    }
}
