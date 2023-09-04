<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\DepositService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    private DepositService $depositService;

    public function __construct(DepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    public function index(BObject $object, Request $request): View
    {
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());

        $total = [];
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        $deposits = $this->depositService->filterDeposit($requestData, $total);

        $paymentQuery = Payment::select('object_id', 'amount');
        $objectPayments = (clone $paymentQuery)->where('object_id', $object->id)->get();

        $object->total_pay = $objectPayments->where('amount', '<', 0)->sum('amount');
        $object->total_receive = $objectPayments->sum('amount') - $object->total_pay;
        $object->total_balance = $object->total_pay + $object->total_receive;
        $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');
        if ($object->code === '288') {
            $object->general_balance_1 = $object->generalCosts()->where('is_pinned', false)->sum('amount');
            $object->general_balance_24 = $object->generalCosts()->where('is_pinned', true)->sum('amount');
        }
        $statuses = [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'В архиве',
            Status::STATUS_DELETED => 'Удален'
        ];

        return view('objects.tabs.deposits', compact('statuses', 'object', 'deposits', 'objects', 'contracts', 'total'));
    }
}
