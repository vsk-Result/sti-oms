<?php

namespace App\Http\Controllers\Pivot\Debt;

use App\Http\Controllers\Controller;
use App\Services\DebtService;
use Illuminate\View\View;
use function view;

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
