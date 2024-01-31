<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\BankGuaranteeService;
use App\Services\Contract\ActService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BankGuaranteeController extends Controller
{
    private BankGuaranteeService $guaranteeService;

    public function __construct(BankGuaranteeService $guaranteeService)
    {
        $this->guaranteeService = $guaranteeService;
    }

    public function index(BObject $object, Request $request): View
    {
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());

        $total = [];
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        $bankGuarantees = $this->guaranteeService->filterBankGuarantee($requestData, $total);

        $statuses = [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'В архиве',
            Status::STATUS_DELETED => 'Удален'
        ];

        return view('objects.tabs.bank_guarantees', compact('statuses', 'object', 'bankGuarantees', 'objects', 'contracts', 'total'));
    }
}
