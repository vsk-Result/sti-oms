<?php

namespace App\Exports\PaymentImport\InvalidBalance;

use App\Exports\PaymentImport\Sheets\AnalysisSheet;
use App\Models\PaymentImport;
use App\Services\PaymentImport\Type\StatementImportService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    public function __construct(private PaymentImport $import, private StatementImportService $importService){}

    public function sheets(): array
    {
        return [new AnalysisSheet($this->import, $this->importService)];
    }
}
