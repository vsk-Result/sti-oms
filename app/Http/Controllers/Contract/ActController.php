<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract\Act;
use App\Models\Contract\Contract;
use App\Models\Status;
use App\Services\Contract\ActService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function index(): View
    {
        $acts = Act::with('payments')->get();
        return view('acts.index', compact('acts'));
    }

    public function create(Request $request): View
    {
        $objectId = $request->get('current_object_id') ?? null;
        $query = Contract::query();

        if ($objectId) {
            $query->where('object_id', $objectId);
        }

        $contracts = $query->where('type_id', Contract::TYPE_MAIN)->get();
        return view('acts.create', compact('contracts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->actService->createAct($request->toArray());
        return redirect($request->get('return_url') ?? route('acts.index'));
    }

    public function show(Act $act): View
    {
        return view('acts.show', compact('act'));
    }

    public function edit(Act $act): View
    {
        $contracts = Contract::where('type_id', Contract::TYPE_MAIN)->get();
        $statuses = Status::getStatuses();
        return view('acts.edit', compact('act', 'contracts', 'statuses'));
    }

    public function update(Act $act, Request $request): RedirectResponse
    {
        $this->actService->updateAct($act, $request->toArray());
        return redirect($request->get('return_url') ?? route('acts.index'));
    }

    public function destroy(Act $act): RedirectResponse
    {
        $this->actService->destroyAct($act);
        return redirect()->back();
    }
}
