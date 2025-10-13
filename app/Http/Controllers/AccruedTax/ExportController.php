<?php

namespace App\Http\Controllers\AccruedTax;

use App\Exports\AccruedTax\Export;
use App\Http\Controllers\Controller;
use App\Services\AccruedTax\AccruedTaxService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(private AccruedTaxService $accruedTaxService) {}

    public function store(): BinaryFileResponse
    {
        $names = $this->accruedTaxService->getNames();
        $taxes = $this->accruedTaxService->getTaxes();
        $dates = $this->accruedTaxService->getDates();

        return Excel::download(new Export($names, $taxes, $dates), 'Начисленные налоги на ' . now()->format('d.m.Y') . '.xlsx');
    }
}
