<?php

namespace App\Exports\PaymentImport\Sheets;

use App\Models\PaymentImport;
use App\Services\PaymentImport\Type\StatementImportService;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalysisSheet implements
    WithTitle,
    ShouldAutoSize,
    WithStyles
{
    public function __construct(private PaymentImport $import, private StatementImportService $importService) {}

    public function title(): string
    {
        return 'Анализ оплат';
    }

    public function styles(Worksheet $sheet): void
    {
        $statementData = $this->importService->getStatementDataFromExcel($requestData['file']);
        $processInfo = $this->importService->processInfoFromStatementData($statementData);
    }
}
