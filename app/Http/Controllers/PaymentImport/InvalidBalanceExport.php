<?php

namespace App\Http\Controllers\PaymentImport;

use App\Exports\PaymentImport\InvalidBalance\Export;
use App\Http\Controllers\Controller;
use App\Models\PaymentImport;
use App\Services\PaymentImport\Type\StatementImportService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvalidBalanceExport extends Controller
{
    public function __construct(private StatementImportService $importService) {}

    public function store(PaymentImport $import): BinaryFileResponse
    {
        return Excel::download(
            new Export($import, $this->importService),
            'Анализ несоответствия баланса.xlsx'
        );
    }
}
