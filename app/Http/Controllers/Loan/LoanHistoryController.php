<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanHistory;
use App\Models\Status;
use App\Services\LoanHistoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanHistoryController extends Controller
{
    private LoanHistoryService $loanHistoryService;

    public function __construct(LoanHistoryService $loanHistoryService)
    {
        $this->loanHistoryService = $loanHistoryService;
    }

    public function index(Loan $loan): View
    {
        $historyPayments = $loan->historyPayments()->orderBy('date')->get();
        return view('loans.history.index', compact('loan', 'historyPayments'));
    }

    public function create(Loan $loan): View
    {
        return view('loans.history.create', compact('loan'));
    }

    public function store(Request $request, Loan $loan): RedirectResponse
    {
        $this->loanHistoryService->createLoanHistory($loan, $request->toArray());
        return redirect($request->get('return_url') ?? route('loans.history.index', $loan));
    }

    public function edit(Loan $loan, LoanHistory $history): View
    {
        $statuses = Status::getStatuses();
        return view('loans.history.edit', compact('loan', 'history', 'statuses'));
    }

    public function update(Loan $loan, LoanHistory $history, Request $request): RedirectResponse
    {
        $this->loanHistoryService->updateLoanHistory($loan, $history, $request->toArray());
        return redirect($request->get('return_url') ?? route('loans.history.index', $loan));
    }

    public function destroy(Loan $loan, LoanHistory $history): RedirectResponse
    {
        $this->loanHistoryService->destroyLoanHistory($loan, $history);
        return redirect()->route('loans.history.index', $loan);
    }

    public function reload(Loan $loan): RedirectResponse
    {
        $this->loanHistoryService->reloadLoanHistory($loan);
        return redirect()->back();
    }
}
