<?php

namespace App\Http\Controllers\DebtImport;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ImportManualController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $objectId = $request->get('object_id');
        $file = $request->file('file');

        $object = BObject::find($objectId);

        // Закинем в папку для ручных загрузок
        Storage::putFileAs('public/objects-debts-manuals', $file, $object->code . '.xlsx');

        // Запустим крон задачу на импорт долгов по подрядчикам
        Artisan::call('oms:import-contractor-debts-from-manager-excel');

        return redirect()->back();
    }
}
