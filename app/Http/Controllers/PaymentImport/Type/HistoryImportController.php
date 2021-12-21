<?php

namespace App\Http\Controllers\PaymentImport\Type;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\PaymentImport\Type\HistoryImportImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HistoryImportController extends Controller
{
    private HistoryImportImportService $importService;

    public function __construct(HistoryImportImportService $importService)
    {
        $this->importService = $importService;
    }

    public function create(): View
    {
        $objects = BObject::orderBy('code')->get();
        return view('payment-imports.types.history.create', compact('objects'));
    }

    public function store(Request $request): RedirectResponse
    {
        ini_set('max_execution_time', 600);
        $import = $this->importService->createImport($request->toArray());
        return redirect()->route('payment_imports.edit', $import);
    }
}
