<?php

namespace App\Http\Controllers\PaymentImport\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentImport\StorePaymentImportRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Services\PaymentImport\Type\PaymentImportService;
use App\Services\PaymentImport\PaymentImportService as PIService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentImportController extends Controller
{
    private PaymentImportService $importService;
    private PIService $mainImportService;

    public function __construct(PaymentImportService $importService, PIService $mainImportService)
    {
        $this->importService = $importService;
        $this->mainImportService = $mainImportService;
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

        if ($this->importService->hasError()) {
            $this->mainImportService->destroyImport($import);
            session()->flash('status', $this->importService->getError());
            return redirect()->back();
        }

        return redirect()->route('payment_imports.edit', $import);
    }
}
