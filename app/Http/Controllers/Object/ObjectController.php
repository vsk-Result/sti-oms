<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Http\Requests\Object\StoreOrUpdateObjectRequest;
use App\Models\Object\BObject;
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
        $objects = BObject::withCount(['payments' => function($query) {
            $query->whereDate('date', '>', Carbon::now()->subWeeks(2)->format('Y-m-d'));
        }])->orderByDesc('payments_count')->orderByDesc('code')->get();

        return view('objects.index', compact('objects'));
    }

    public function create(): View
    {
        return view('objects.create');
    }

    public function store(StoreOrUpdateObjectRequest $request): RedirectResponse
    {
        $this->objectService->createObject($request->toArray());
        return redirect()->route('objects.index');
    }

    public function show(BObject $object): RedirectResponse
    {
        return redirect()->route('objects.pivot.index', compact('object'));
    }

    public function edit(BObject $object): View
    {
        $statuses = Status::getStatuses();
        return view('objects.edit', compact('object', 'statuses'));
    }

    public function update(BObject $object, StoreOrUpdateObjectRequest $request): RedirectResponse
    {
        $this->objectService->updateObject($object, $request->toArray());
        return redirect()->route('objects.index');
    }
}
