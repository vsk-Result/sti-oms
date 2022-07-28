<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
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

        $paymentQuery = Payment::select('object_id', 'amount');
        $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

        $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
        $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
        $object->total_balance = $object->total_pay + $object->total_receive;
        $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');

        return view('objects.tabs.bank_guarantees', compact('object', 'bankGuarantees', 'objects', 'contracts', 'total'));
    }
}
