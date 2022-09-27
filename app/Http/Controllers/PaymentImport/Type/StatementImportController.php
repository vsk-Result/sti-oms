<?php

namespace App\Http\Controllers\PaymentImport\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statement\StoreStatementRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Currency;
use App\Services\PaymentImport\PaymentImportService;
use App\Services\PaymentImport\Type\StatementImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StatementImportController extends Controller
{
    private StatementImportService $importService;
    private PaymentImportService $mainImportService;

    public function __construct(StatementImportService $importService, PaymentImportService $mainImportService)
    {
        $this->importService = $importService;
        $this->mainImportService = $mainImportService;
    }

    public function create(): View
    {
        $banks = Bank::getBanks() + [null => 'Без банка'];
        $companies = Company::all();
        $currencies = Currency::getCurrencies();
        return view('payment-imports.types.statements.create', compact('banks', 'companies', 'currencies'));
    }

    public function store(StoreStatementRequest $request): RedirectResponse
    {
        $import = $this->importService->createImport($request->toArray());

        if ($this->importService->hasError()) {
            $this->mainImportService->destroyImport($import);
            session()->flash('status', $this->importService->getError());
            return redirect()->back();
        }

        return redirect()->route('payment_imports.edit', $import);
    }
}
