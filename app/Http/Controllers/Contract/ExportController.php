<?php

namespace App\Http\Controllers\Contract;

use App\Exports\Contract\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Contract\ContractService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ContractService $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $total = [];
        $object = BObject::where('id', $request->get('object_id'))->first();
        $contracts = $this->contractService->filterContracts($request->toArray(), $total, false);

        return Excel::download(new Export($object, $contracts, $total), 'Справка объекта ' . $object->getName() . ' на ' . now()->format('d.m.Y') . '.xlsx');
    }
}
