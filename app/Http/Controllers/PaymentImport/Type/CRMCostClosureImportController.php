<?php

namespace App\Http\Controllers\PaymentImport\Type;

use App\Http\Controllers\Controller;
use App\Services\PaymentImport\Type\CRMCostClosureImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\PaymentImport\PaymentImportService;


class CRMCostClosureImportController extends Controller
{
    private CRMCostClosureImportService $importService;
    private PaymentImportService $mainImportService;

    public function __construct(CRMCostClosureImportService $importService, PaymentImportService $mainImportService)
    {
        $this->importService = $importService;
        $this->mainImportService = $mainImportService;
    }

    public function create(): View
    {
        $closures = $this->importService->getClosures();
        return view('payment-imports.types.crm-cost-closures.create', compact('closures'));
    }

    public function store(Request $request): RedirectResponse
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
