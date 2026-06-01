<?php

namespace App\Http\Controllers\Guarantee;

use App\Exports\Guarantee\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\GuaranteeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private GuaranteeService $guaranteeService;

    public function __construct(GuaranteeService $guaranteeService)
    {
        $this->guaranteeService = $guaranteeService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $total = [];
        $object = BObject::find($request->get('object_id')[0] ?? '');
        $guarantees = $this->guaranteeService->filterGuarantee($request->toArray(), $total, false);

        if ($object) {
            $name = 'Справка ГУ объекта ' . $object->getName() . ' на ' . now()->format('d.m.Y') . '.xlsx';
        } else {
            $name = 'Справка ГУ на ' . now()->format('d.m.Y') . '.xlsx';
        }

        return Excel::download(new Export($guarantees, $total, $name), $name);
    }
}
