<?php

namespace App\Http\Controllers\PaymentImport;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Object\BObject;
use App\Services\PaymentImport\PaymentImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    private PaymentImportService $importService;

    public function __construct(PaymentImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index(Request $request): View
    {
        $companies = Company::orderBy('name')->get();
        $types = PaymentImport::getTypes();
        $banks = Bank::getBanks();
        $statuses = PaymentImport::getStatusesLists();

        $importsPaginated = $this->importService->filterImport($request->toArray());
        $importsGroupedByDate = collect($importsPaginated->items())->groupBy('date');

        return view('payment-imports.index', compact('importsPaginated', 'importsGroupedByDate', 'companies', 'types', 'banks', 'statuses'));
    }

    public function show(PaymentImport $import): View
    {
        $import->load([
            'payments' => function($query) {
                $query->orderByDesc('amount');
            },
            'payments.organizationSender',
            'payments.organizationReceiver',
            'payments.object',
        ]);
        return view('payment-imports.show', compact('import'));
    }

    public function edit(PaymentImport $import): View
    {
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();
        $import->load([
            'payments' => function($query) {
                $query->orderByDesc('amount');
            },
            'payments.organizationSender',
            'payments.organizationReceiver',
            'payments.object',
        ]);
        return view('payment-imports.edit', compact('import', 'objects', 'categories'));
    }

    public function destroy(PaymentImport $import): RedirectResponse
    {
        $this->importService->destroyImport($import);
        return redirect()->route('payment_imports.index');
    }
}
