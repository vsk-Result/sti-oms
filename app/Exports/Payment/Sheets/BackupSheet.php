<?php

namespace App\Exports\Payment\Sheets;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BackupSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    public function __construct(string $sheetName)
    {
        $this->sheetName = $sheetName;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Дата');
        $sheet->setCellValue('C1', 'Статья затрат');
        $sheet->setCellValue('D1', 'Описание');

        $row = 2;
        Payment::chunk(5000, function($payments) use($sheet, &$row) {
            foreach ($payments as $payment) {
                $sheet->setCellValue('A' . $row, $payment->id);
                $sheet->setCellValue('B' . $row, Date::dateTimeToExcel(Carbon::parse($payment->date)));
                $sheet->setCellValue('C' . $row, $payment->code . ' ');
                $sheet->setCellValue('D' . $row, $payment->description);

                $row++;
            }
        });
    }
}
