<?php

namespace App\Http\Controllers\DebtImport;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use Illuminate\Http\RedirectResponse;

class ManualReplaceResetController extends Controller
{
    public function store(BObject $object): RedirectResponse
    {
        PivotObjectDebt::where('object_id', $object->id)
            ->whereIn('debt_source_id', PivotObjectDebt::getManualSources())
            ->delete();

        return redirect()->back();
    }
}
