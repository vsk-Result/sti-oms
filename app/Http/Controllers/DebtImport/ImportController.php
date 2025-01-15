<?php

namespace App\Http\Controllers\DebtImport;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebtImport\StoreDebtImportRequest;
use App\Models\Company;
use App\Models\Debt\DebtImport;
use App\Services\DebtImport\DebtImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ImportController extends Controller
{
    public function __construct(private DebtImportService $importService) {}

    public function index(): View
    {
        $imports = DebtImport::orderByDesc('date')->orderByDesc('id')->paginate(15);

        return view('debt-imports.index', compact('imports'));
    }

    public function create(): View
    {
        $companies = Company::all();
        return view('debt-imports.create', compact('companies'));
    }

    public function store(StoreDebtImportRequest $request): RedirectResponse
    {
        $status = $this->importService->createImport($request->toArray());

        if ($status !== 'ok') {
            session()->flash('status', $status);
            return redirect()->back();
        }

        return redirect()->route('debt_imports.index');
    }

    public function destroy(DebtImport $import): RedirectResponse
    {
        $this->importService->destroyImport($import);
        return redirect()->route('debt_imports.index');
    }
}
