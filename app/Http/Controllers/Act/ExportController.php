<?php

namespace App\Http\Controllers\Act;

use App\Exports\Act\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $total = [];
        $object = BObject::find($request->get('object_id')[0]);
        $acts = $this->actService->filterActs($request->toArray(), $total, false);

        return Excel::download(new Export($acts, $total), 'Справка актов объекта ' . $object->getName() . ' на ' . now()->format('d.m.Y') . '.xlsx');
    }
}
