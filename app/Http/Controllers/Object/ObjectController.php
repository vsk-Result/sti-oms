<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreObjectRequest;
use App\Http\Requests\Object\UpdateObjectRequest;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Status;
use App\Services\ObjectService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ObjectController extends Controller
{
    private ObjectService $objectService;

    public function __construct(ObjectService $objectService)
    {
        $this->objectService = $objectService;
    }

    public function index(Request $request): View|JsonResponse
    {
        if (! request()->ajax()) {
            return view('objects.index');
        }

        $query = BObject::query();

        if (! empty($request->input('q'))) {
            $filterName = $request->input('q');
            $query->where(function($q) use($filterName) {
                $q->where('name', 'LIKE', '%' . $filterName . '%');
                $q->orWhere('code', 'LIKE', '%' . $filterName . '%');
            });
        }

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $query->whereIn('id', auth()->user()->objects->pluck('id'));
        }

        $paymentQuery = Payment::select('object_id', 'amount');
        $objects = $query->orderByRaw('CONVERT(code, SIGNED) desc')->paginate(10)->withQueryString();

        foreach ($objects as $object) {
            $object->total_pay = (clone $paymentQuery)->where('object_id', $object->id)->where('amount', '<', 0)->sum('amount');
            $object->total_receive = (clone $paymentQuery)->where('object_id', $object->id)->sum('amount') - $object->total_pay;
            $object->total_balance = $object->total_pay + $object->total_receive;
            $object->total_with_general_balance = $object->total_pay + $object->total_receive + $object->generalCosts()->sum('amount');
            if ($object->code === '288') {
                $object->general_balance_1 = $object->generalCosts()->where('is_pinned', false)->sum('amount');
                $object->general_balance_24 = $object->generalCosts()->where('is_pinned', true)->sum('amount');
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Объекты успешно получены',
            'objects_view' => view('objects.parts._objects', compact('objects'))->render()
        ]);
    }

    public function create(): View
    {
        $organizations = Organization::orderBy('name')->get();
        return view('objects.create', compact('organizations'));
    }

    public function store(StoreObjectRequest $request): RedirectResponse
    {
        $this->objectService->createObject($request->toArray());
        return redirect()->route('objects.index');
    }

    public function show(BObject $object): RedirectResponse
    {
        return redirect('/objects/' . $object->id . '/payments?object_id%5B%5D=' . $object->id);
    }

    public function edit(BObject $object): View
    {
        $statuses = $object->getStatuses();
        $organizations = Organization::orderBy('name')->get();
        $object->load('customers');
        return view('objects.edit', compact('object', 'statuses', 'organizations'));
    }

    public function update(BObject $object, UpdateObjectRequest $request): RedirectResponse
    {
        $this->objectService->updateObject($object, $request->toArray());
        return redirect()->route('objects.show', $object);
    }
}
