<?php

namespace App\Http\Controllers\Pivot;

use App\Http\Controllers\Controller;
use App\Services\DebtService;
use Illuminate\View\View;

class DebtController extends Controller
{
    private DebtService $debtService;

    public function __construct(DebtService $debtService)
    {
        $this->debtService = $debtService;
    }

    public function index(): View
    {
        $pivot = $this->debtService->getPivot();
        return view('pivots.debts.index', compact('pivot'));
    }
}
