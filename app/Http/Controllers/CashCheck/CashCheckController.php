<?php

namespace App\Http\Controllers\CashCheck;

use App\Http\Controllers\Controller;
use App\Models\CashCheck\CashCheck;
use App\Services\CashCheck\CashCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashCheckController extends Controller
{
    public function __construct(private CashCheckService $cashCheckService) {}

    public function index(Request $request): View
    {
        $statuses = CashCheck::getStatuses();
        $uncheckedChecks = $this->cashCheckService->getCashChecks($request->toArray());
        $uncheckedChecksGroupedByPeriod = collect($uncheckedChecks->items())->groupBy('period');

        return view('crm-cash-checks.index', compact('uncheckedChecksGroupedByPeriod', 'uncheckedChecks', 'statuses'));
    }

    public function store(Request $request): JsonResponse
    {
        $check = $this->cashCheckService->findCashCheck($request->toArray());

        if ($check) {
            return response()->json(['isset_cash_check' => true, 'cash_check_status' => $check->status_id]);
        }

        $check = $this->cashCheckService->createCashCheck($request->toArray());
        $managers = $this->cashCheckService->getManagersForCheckFromExcel($check);
        $this->cashCheckService->addCheckManagers($check, $managers);

        return response()->json(['isset_cash_check' => false]);
    }

    public function show(CashCheck $check): View
    {
        $details = $this->cashCheckService->getCashCheckDetails($check);

        return view('crm-cash-checks.show', compact('check', 'details'));
    }
}
