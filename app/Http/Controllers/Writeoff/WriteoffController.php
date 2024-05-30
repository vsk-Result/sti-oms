<?php

namespace App\Http\Controllers\Writeoff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Writeoff\StoreWriteoffRequest;
use App\Http\Requests\Writeoff\UpdateWriteoffRequest;
use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Models\Writeoff;
use App\Services\WriteoffService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WriteoffController extends Controller
{
    private WriteoffService $writeoffService;

    public function __construct(WriteoffService $writeoffService)
    {
        $this->writeoffService = $writeoffService;
    }

    public function index(Request $request): View
    {
        $objects = BObject::orderBy('code')->get();

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $companies = Company::orderBy('id')->get();

        $totalInfo = [];
        $writeoffs = $this->writeoffService->filterWriteoff($request->toArray(), $totalInfo);

        return view(
            'writeoffs.index',
            compact(
                'writeoffs', 'companies', 'objects', 'totalInfo'
            )
        );
    }

    public function create(Request $request): View
    {
        $objectId = $request->get('current_object_id') ?? null;

        $objects = BObject::orderBy('code')->get();
        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $companies = Company::orderBy('id')->get();

        return view('writeoffs.create', compact( 'objects', 'companies', 'objectId'));
    }

    public function store(StoreWriteoffRequest $request): RedirectResponse
    {
        $this->writeoffService->createWriteoff($request->toArray());
        return redirect($request->get('return_url') ?? route('writeoffs.index'));
    }

    public function show(Writeoff $writeoff): View
    {
        return view('writeoffs.show', compact('writeoff'));
    }

    public function edit(Writeoff $writeoff): View
    {
        $objects = BObject::orderBy('code')->get();
        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $companies = Company::orderBy('id')->get();
        $statuses = Status::getStatuses();

        return view('writeoffs.edit', compact('writeoff', 'objects', 'companies', 'statuses'));
    }

    public function update(Writeoff $writeoff, UpdateWriteoffRequest $request): RedirectResponse
    {
        $this->writeoffService->updateWriteoff($writeoff, $request->toArray());
        return redirect($request->get('return_url') ?? route('writeoffs.index'));
    }

    public function destroy(Writeoff $writeoff): RedirectResponse
    {
        $this->writeoffService->destroyWriteoff($writeoff);
        return redirect()->back();
    }
}
