<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Services\Contract\ContractService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    private ContractService $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index(BObject $object, Request $request): View
    {
        $total = [];
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());
        $contracts = $this->contractService->filterContracts($requestData, $total);
        $objects = BObject::orderBy('code')->get();

        return view('objects.tabs.contracts', compact('object', 'contracts', 'objects', 'total'));
    }
}
