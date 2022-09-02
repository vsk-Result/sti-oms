<?php

namespace App\Http\Controllers\PaymentImport\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statement\StoreStatementRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Services\PaymentImport\Type\StatementImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StatementImportController extends Controller
{
    private StatementImportService $importService;

    public function __construct(StatementImportService $importService)
    {
        $this->importService = $importService;
    }

    public function create(): View
    {
        $banks = Bank::getBanks() + [null => 'Без банка'];
        $companies = Company::all();
        $currencies = ['RUB', 'EUR'];
        return view('payment-imports.types.statements.create', compact('banks', 'companies', 'currencies'));
    }

    public function store(StoreStatementRequest $request): RedirectResponse
    {
        $import = $this->importService->createImport($request->toArray());
        return redirect()->route('payment_imports.edit', $import);
    }
}
