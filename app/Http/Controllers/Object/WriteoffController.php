<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreOrUpdateObjectRequest;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\GuaranteeService;
use App\Services\WriteoffService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WriteoffController extends Controller
{
    private WriteoffService $writeoffService;

    public function __construct(WriteoffService $writeoffService)
    {
        $this->writeoffService = $writeoffService;
    }

    public function index(BObject $object, Request $request): View
    {
        $requestData = array_merge(['object_id' => [$object->id]], $request->toArray());

        $total = [];
        $objects = BObject::orderBy('code')->get();

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $companies = Company::orderBy('name')->get();

        $totalInfo = [];
        $writeoffs = $this->writeoffService->filterWriteoff($requestData, $totalInfo);

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

        return view('objects.tabs.writeoffs', compact('object', 'objects', 'companies', 'writeoffs', 'totalInfo'));
    }
}
