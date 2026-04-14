<?php

namespace App\Services;

use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Models\Object\TransferService;

class ObjectGeneralCostService
{
    public static function updateGeneralCost(BObject $object, float $amount, bool $pinned = false, bool $needDelete = true): void
    {
        if ($needDelete) {
            GeneralCost::where('object_id', $object->id)->where('nds_type', 'nds')->delete();
        }

        $generalCost = new GeneralCost();
        $generalCost->object_id = $object->id;
        $generalCost->amount = $amount;
        $generalCost->amount_without_nds = 0;
        $generalCost->is_pinned = $pinned;
        $generalCost->nds_type = 'nds';
        $generalCost->save();
    }

    public static function updateGeneralCostWithoutNDS(BObject $object, float $amount, bool $pinned = false, bool $needDelete = true): void
    {
        if ($needDelete) {
            GeneralCost::where('object_id', $object->id)->where('nds_type', 'without_nds')->delete();
        }

        $generalCost = new GeneralCost();
        $generalCost->object_id = $object->id;
        $generalCost->amount = 0;
        $generalCost->amount_without_nds = $amount;
        $generalCost->is_pinned = $pinned;
        $generalCost->nds_type = 'without_nds';
        $generalCost->save();
    }

    public static function updateDistributionTransferService(BObject $object, float $amount, bool $needDelete = true): void
    {
        if ($needDelete) {
            TransferService::where('object_id', $object->id)->delete();
        }

        $generalCost = new TransferService();
        $generalCost->object_id = $object->id;
        $generalCost->amount = $amount;
        $generalCost->save();
    }
}
