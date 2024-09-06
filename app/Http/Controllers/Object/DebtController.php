<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Debt\DebtImport;
use App\Models\Debt\DebtManual;
use App\Models\Object\BObject;
use App\Services\UploadDebtStatusService;
use Illuminate\Contracts\View\View;

class DebtController extends Controller
{
    private UploadDebtStatusService $uploadDebtStatusService;

    public function __construct(UploadDebtStatusService $uploadDebtStatusService)
    {
        $this->uploadDebtStatusService = $uploadDebtStatusService;
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

            if ($object->code === '000') {
                $hasObjectImportLink = 'public/objects-debts-manuals/' . $object->code . '.xlsx';
            }
            $hasObjectImportId = $debtObjectImport->id;
        }

        $updatedInfo = $this->uploadDebtStatusService->getUpdatedInfoForObject($object);

        $contractorsUpdated = $updatedInfo['contractors'];
        $serviceUpdated = $updatedInfo['service'];
        $providersUpdated = $updatedInfo['providers'];

        return view('objects.tabs.debts', compact(
                'object', 'debtManuals', 'hasObjectImportLink', 'hasObjectImport',
                'hasObjectImportId', 'contractorsUpdated', 'serviceUpdated', 'providersUpdated'
            )
        );
    }
}
