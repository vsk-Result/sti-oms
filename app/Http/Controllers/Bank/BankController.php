<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Status;
use App\Services\BankService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankController extends Controller
{
    private BankService $bankService;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }

    public function index(): View
    {
        $banks = Bank::orderBy('id')->get();
        return view('banks.index', compact('banks'));
    }

    public function create(): View
    {
        return view('banks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->bankService->createBank($request->toArray());
        return redirect()->route('banks.index');
    }

    public function edit(Bank $bank): View
    {
        $statuses = Status::getStatuses();
        return view('banks.edit', compact('bank', 'statuses'));
    }

    public function update(Bank $bank, Request $request): RedirectResponse
    {
        $this->bankService->updateBank($bank, $request->toArray());
        return redirect()->route('banks.index');
    }

    public function destroy(Bank $bank): RedirectResponse
    {
        $this->bankService->destroyBank($bank);
        return redirect()->route('banks.index');
    }
}
