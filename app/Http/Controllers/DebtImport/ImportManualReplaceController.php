<?php

namespace App\Http\Controllers\DebtImport;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ImportManualReplaceController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $objectId = $request->get('object_id');
        $file = $request->file('file');

        $object = BObject::find($objectId);

        // Закинем в папку для ручных загрузок
        Storage::putFileAs('public/objects-debts-manuals', $file, $object->code . '__manual_replace.xlsx');

        // Запустим крон задачу на импорт долгов
        Artisan::call('oms:import-manual-replace-debts-from-excel');

        return redirect()->back();
    }
}
