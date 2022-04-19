<?php

namespace App\Http\Controllers\DebtImport;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Services\DebtImportService;
use App\Services\DebtService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\DebtImport\StoreDebtImportRequest;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    private DebtImportService $importService;
    private DebtService $debtService;

    public function __construct(DebtImportService $importService, DebtService $debtService)
    {
        $this->importService = $importService;
        $this->debtService = $debtService;
    }

    public function index(): View
    {
        $imports = DebtImport::with('company', 'debts', 'createdBy')->orderByDesc('date')->orderByDesc('id')->get();
        return view('debt-imports.index', compact('imports'));
    }

    public function create(): View
    {
        $companies = Company::all();
        return view('debt-imports.create', compact('companies'));
    }

    public function store(StoreDebtImportRequest $request): RedirectResponse
    {
        $this->importService->createImport($request->toArray());
        return redirect()->route('debt_imports.index');
    }

    public function destroy(DebtImport $import): RedirectResponse
    {
        $this->importService->destroyImport($import);
        return redirect()->route('debt_imports.index');
    }
}
