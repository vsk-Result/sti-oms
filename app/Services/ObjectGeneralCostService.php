<?php

namespace App\Services;

use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;

class ObjectGeneralCostService
{
    public static function updateGeneralCost(BObject $object, float $amount, bool $pinned = false, bool $needDelete = true): void
    {
        if ($needDelete) {
            GeneralCost::where('object_id', $object->id)->delete();
        }

        $generalCost = new GeneralCost();
        $generalCost->object_id = $object->id;
        $generalCost->amount = $amount;
        $generalCost->is_pinned = $pinned;
        $generalCost->save();
    }
}
