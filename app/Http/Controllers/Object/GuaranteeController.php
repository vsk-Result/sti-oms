<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreOrUpdateObjectRequest;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\GuaranteeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuaranteeController extends Controller
{
    private GuaranteeService $guaranteeService;

    public function __construct(GuaranteeService $guaranteeService)
    {
        $this->guaranteeService = $guaranteeService;
    }

    public function index(BObject $object, Request $request): View
    {
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());

        $total = [];
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        $guarantees = $this->guaranteeService->filterGuarantee($requestData, $total);

        return view('objects.tabs.guarantees', compact('object', 'objects', 'contracts', 'guarantees', 'total'));
    }
}
