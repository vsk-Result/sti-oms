<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreObjectRequest;
use App\Http\Requests\Object\UpdateObjectRequest;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ObjectController extends Controller
{
    private ObjectService $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function index(): View
    {
//        $objects = BObject::with('debts', 'payments')->withCount(['payments' => function($query) {
//            $query->whereDate('date', '>', Carbon::now()->subWeeks(2)->format('Y-m-d'));
//        }])->orderByDesc('payments_count')->orderByDesc('code')->get();

        $query = BObject::query();

        if (auth()->user()->hasRole('object-leader')) {
            $query->whereIn('id', auth()->user()->objects->pluck('id'));
        }

        $paymentQuery = Payment::select('object_id', 'amount');
        $objects = $query->orderByDesc('code')->get();

        foreach ($objects as $object) {
            $objectPayments = (clone $paymentQuery)->where('object_id', $object->id);
            $object->total_pay = (clone $objectPayments)->where('amount', '<', 0)->sum('amount');
            $object->total_receive = (clone $objectPayments)->sum('amount') - $object->total_pay;
            $object->total_balance = $object->total_pay + $object->total_receive;
        }

        return view('objects.index', compact('objects'));
    }

    public function create(): View
    {
        return view('objects.create');
    }

    public function store(StoreObjectRequest $request): RedirectResponse
    {
        $this->objectService->createObject($request->toArray());
        return redirect()->route('objects.index');
    }

    public function show(BObject $object): RedirectResponse
    {
        return redirect()->route('objects.payments.index', compact('object'));
    }

    public function edit(BObject $object): View
    {
        $statuses = Status::getStatuses();
        return view('objects.edit', compact('object', 'statuses'));
    }

    public function update(BObject $object, UpdateObjectRequest $request): RedirectResponse
    {
        $this->objectService->updateObject($object, $request->toArray());
        return redirect()->route('objects.show', $object);
    }
}
