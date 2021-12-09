<?php

namespace App\Http\Controllers\DebtImport;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Debt\DebtImport;
use App\Services\DebtImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\DebtImport\StoreDebtImportRequest;

class ImportController extends Controller
{
    private DebtImportService $importService;

    public function __construct(DebtImportService $importService)
    {
        $this->importService = $importService;
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

    public function show(DebtImport $import): View
    {
        $import->load([
            'debts' => function($query) {
                $query->orderByDesc('amount');
            },
            'debts.organization',
            'debts.object',
        ]);
        return view('debt-imports.show', compact('import'));
    }
//
//    public function edit(PaymentImport $import): View
//    {
//        $categories = Payment::getCategories();
//        $objects = Payment::getTypes() + BObject::getObjectsList();
//        $import->load([
//            'payments' => function($query) {
//                $query->orderByDesc('amount');
//            },
//            'payments.organizationSender',
//            'payments.organizationReceiver',
//            'payments.object',
//        ]);
//        return view('payment-imports.edit', compact('import', 'objects', 'categories'));
//    }
//
//    public function destroy(PaymentImport $import): RedirectResponse
//    {
//        $this->importService->destroyImport($import);
//        return redirect()->route('payment_imports.index');
//    }
}
