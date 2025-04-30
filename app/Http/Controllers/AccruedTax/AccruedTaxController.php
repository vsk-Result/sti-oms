<?php

namespace App\Http\Controllers\AccruedTax;

use App\Http\Controllers\Controller;
use App\Services\AccruedTax\AccruedTaxService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccruedTaxController extends Controller
{
    public function __construct(private AccruedTaxService $accruedTaxService) {}

    public function index(): View
    {
        $names = $this->accruedTaxService->getNames();
        $taxes = $this->accruedTaxService->getTaxes();
        $dates = $this->accruedTaxService->getDates();
        return view('accrued_taxes.index', compact('taxes', 'names', 'dates'));
    }

    public function update(Request $request): JsonResponse
    {
        $this->accruedTaxService->updateTax($request->toArray());
        return response()->json(['status' => 'success', 'message' => 'Данные успешно обновлены!']);
    }
}
