<?php

namespace App\Http\Controllers\PaymentImport;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Models\Object\BObject;
use App\Services\PaymentImport\PaymentImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ImportController extends Controller
{
    private PaymentImportService $importService;

    public function __construct(PaymentImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index(): View
    {
        $data = simplexml_load_file('http://www.cbr.ru/scripts/XML_daily.asp?date_req=15/11/2021');
        foreach ($data as $d) {
            dd($d);
        }
        $imports = PaymentImport::with('company', 'createdBy')->orderByDesc('date')->orderByDesc('id')->get();
        return view('payment-imports.index', compact('imports'));
    }

    public function show(PaymentImport $import): View
    {
        $import->load(['payments', 'payments.organizationSender', 'payments.organizationReceiver']);
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
            'payments.organizationReceiver'
        ]);
        return view('payment-imports.edit', compact('import', 'objects', 'categories'));
    }

    public function destroy(PaymentImport $import): RedirectResponse
    {
        $this->importService->destroyImport($import);
        return redirect()->route('payment_imports.index');
    }
}
