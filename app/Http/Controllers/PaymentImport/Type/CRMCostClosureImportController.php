<?php

namespace App\Http\Controllers\PaymentImport\Type;

use App\Http\Controllers\Controller;
use App\Services\PaymentImport\Type\CRMCostClosureImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CRMCostClosureImportController extends Controller
{
    private CRMCostClosureImportService $importService;

    public function __construct(CRMCostClosureImportService $importService)
    {
        $this->importService = $importService;
    }

    public function create(): View
    {
        $closures = $this->importService->getClosures();
        return view('payment-imports.types.crm-cost-closures.create', compact('closures'));
    }

    public function store(Request $request): RedirectResponse
    {
        $import = $this->importService->createImport($request->toArray());
        return redirect()->route('payment_imports.edit', $import);
    }
}
