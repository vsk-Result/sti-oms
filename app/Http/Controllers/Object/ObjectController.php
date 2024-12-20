<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreObjectRequest;
use App\Http\Requests\Object\UpdateObjectRequest;
use App\Models\FinanceReport;
use App\Models\Object\BObject;
use App\Models\Object\ResponsiblePerson;
use App\Models\Object\ResponsiblePersonPosition;
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

        if ($request->input('status', '') !== 'all') {
            $query->where('status_id', $request->input('status'));
        }

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $query->whereIn('id', auth()->user()->objects->pluck('id'));
        }

        $objects = $query->orderByRaw('CONVERT(code, SIGNED) desc')->paginate(10)->withQueryString();

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
        if (auth()->user()->hasRole('super-admin')) {
            $prognozFields = array_merge(FinanceReport::getPrognozFields(), ['Планируемая Рентабельность (материал)' => 'planProfitability_material', 'Планируемая Рентабельность (работы)' => 'planProfitability_rad']);

        } else {
            $prognozFields = FinanceReport::getPrognozFields();
        }

        $positions = ResponsiblePersonPosition::getPositions();

        return view('objects.edit', compact('object', 'statuses', 'organizations', 'prognozFields', 'positions'));
    }

    public function update(BObject $object, UpdateObjectRequest $request): RedirectResponse
    {
        $this->objectService->updateObject($object, $request->toArray());
        return redirect()->route('objects.show', $object);
    }
}
