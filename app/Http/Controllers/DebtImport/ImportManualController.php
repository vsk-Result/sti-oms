<?php

namespace App\Http\Controllers\DebtImport;

use App\Http\Controllers\Controller;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Services\DebtImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ImportManualController extends Controller
{
    private DebtImportService $debtImportService;

    public function __construct(DebtImportService $debtImportService)
    {
        $this->debtImportService = $debtImportService;
    }

    public function store(Request $request): RedirectResponse
    {
        $objectId = $request->get('object_id');
        $file = $request->file('file');

        $object = BObject::find($objectId);

        // Закинем в папку для ручных загрузок
        Storage::putFileAs('public/objects-debts-manuals', $file, $object->code . '.xlsx');

        //Если есть запись о загрузке подрядчиков, удалим
//        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->where('date', now()->format('Y-m-d'))->first();
//        if ($debtImport) {
//            $this->debtImportService->destroyImport($debtImport);
//        }

        // Запустим крон задачу на импорт долгов по подрядчикам
//        Artisan::call('oms:objects-debts-from-excel');

        return redirect()->back();
    }
}
