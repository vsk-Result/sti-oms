<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreOrUpdateObjectRequest;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DebtController extends Controller
{
    private ObjectService $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function index(BObject $object): View
    {
        $debtManuals = DebtManual::where('object_id', $object->id)->get();

        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;

        $hasObjectImport = false;
        $hasObjectImportLink = '';
        $hasObjectImportId = null;

        if ($objectExistInObjectImport) {
            $hasObjectImport = true;
            $hasObjectImportLink = 'debt-imports/objects/' . $debtObjectImport->id . '_' . $object->code . '.xlsx';
            $hasObjectImportId = $debtObjectImport->id;
        }

        return view('objects.tabs.debts', compact('object', 'debtManuals', 'hasObjectImportLink', 'hasObjectImport', 'hasObjectImportId'));
    }
}
