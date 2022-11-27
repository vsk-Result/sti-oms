<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreOrUpdateObjectRequest;
use App\Models\Debt\DebtManual;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DebtController extends Controller
{
    private ObjectService $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function index(BObject $object): View
    {
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

        $debtManuals = DebtManual::where('object_id', $object->id)->get();

        return view('objects.tabs.debts', compact('object', 'debtManuals'));
    }
}
