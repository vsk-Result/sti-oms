<?php

namespace App\Http\Controllers\PaymentImport\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentImport\StorePaymentImportRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Services\PaymentImport\Type\PaymentImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentImportController extends Controller
{
    private PaymentImportService $importService;

    public function __construct(PaymentImportService $importService)
    {
        $this->importService = $importService;
    }

    public function create(): View
    {
        $banks = Bank::getBanks();
        $companies = Company::all();
        return view('payment-imports.types.payments.create', compact('banks', 'companies'));
    }

    public function store(StorePaymentImportRequest $request): RedirectResponse
    {
        $import = $this->importService->createImport($request->toArray());
        return redirect()->route('payment_imports.edit', $import);
    }
}
