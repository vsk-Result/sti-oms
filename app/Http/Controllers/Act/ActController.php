<?php

namespace App\Http\Controllers\Act;

use App\Http\Controllers\Controller;
use App\Models\Contract\Act;
use App\Models\Contract\ActPayment;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Services\Contract\ActService;
use Carbon\Carbon;
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

    public function index(Request $request): View
    {
        $total = [];
        $acts = $this->actService->filterActs($request->toArray(), $total);
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();

        $actsMonths = [];
        foreach (ActPayment::whereIn('act_id', $total['ids'])->orderBy('date')->get() as $payment) {
            if (empty($payment->date)) {
                continue;
            }

            $month = Carbon::parse($payment->date)->format('F Y');
            if (! isset($actsMonths[$month][$payment->currency])) {
                $actsMonths[$month][$payment->currency] = 0;
            }
            $actsMonths[$month][$payment->currency] += $payment->amount;
        }
        foreach ($actsMonths as $k => $currencies) {
            foreach ($currencies as $currency => $amount) {
                $actsMonths[$k][$currency] = $amount;
            }
        }
        $RUBActsAmounts = [];

        foreach ($actsMonths as $currencies) {
            $RUBActsAmounts[] = $currencies['RUB'];
        }

        $actsMonths = array_keys($actsMonths);

        return view('acts.index', compact('acts', 'total', 'objects', 'contracts', 'RUBActsAmounts', 'actsMonths'));
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
