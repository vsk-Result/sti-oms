<?php

namespace App\Exports\CashAccount\Payment\Sheets;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentSheet implements
    WithTitle,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithColumnWidths
{
    private string $sheetName;

    private Builder $payments;

    private int $paymentCount;

    public function __construct(string $sheetName, Builder $payments, int $paymentCount)
    {
        $this->sheetName = $sheetName;
        $this->payments = $payments;
        $this->paymentCount = $paymentCount;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function headings(): array
    {
        return [
            'Тип записи',
            'Объект',
            'Дата записи',
            'Статья расхода/прихода',
            'Сумма',
            'Контрагент',
            'Информация',
            'Категория',
            'ID записи в STI OMS',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $row = 2;
        $this->payments->chunk(2000, function($payments) use($sheet, &$row) {
            foreach ($payments as $payment) {
                $sheet->setCellValue('A' . $row, $payment->getType());
                $sheet->setCellValue('B' . $row, $payment->getObjectCode());
                $sheet->setCellValue('C' . $row, Date::dateTimeToExcel(Carbon::parse($payment->date)));
                $sheet->setCellValue('D' . $row, $payment->code . ' ');
                $sheet->setCellValue('E' . $row, $payment->amount);
                $sheet->setCellValue('F' . $row, $payment->organization?->name ?? '');
                $sheet->setCellValue('G' . $row, $payment->getDescription());
                $sheet->setCellValue('H' . $row, $payment->category);
                $sheet->setCellValue('I' . $row, $payment->id);

                $row++;
            }
        });

        $sheet->getStyle('A1:I' . ($this->paymentCount + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:I' . ($this->paymentCount + 1));
    }

    public function columnWidths(): array
    {
        return [
            'F' => 50,
            'G' => 50,
        ];
    }
}
