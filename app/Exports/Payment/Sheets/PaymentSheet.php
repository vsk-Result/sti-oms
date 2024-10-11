<?php

namespace App\Exports\Payment\Sheets;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
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
    WithColumnFormatting,
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
            'Объект',
            'Расход/Приход',
            'Тип оплаты',
            'Дата оплаты',
            'Код затрат',
            'Сумма c НДС',
            'Сумма без НДС',
            'Контрагент',
            'Информация',
            'Категория',
            'Компания',
            'Банк',
            'ID оплаты в STI OMS',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $row = 2;
        $this->payments->chunk(1000, function($payments) use($sheet, $row) {
            foreach ($payments as $payment) {
                $sheet->setCellValue('A' . $row, $payment->getObject());
                $sheet->setCellValue('B' . $row, $payment->amount < 0 ? 'Расход' : 'Приход');
                $sheet->setCellValue('C' . $row, $payment->getPaymentType());
                $sheet->setCellValue('D' . $row, Date::dateTimeToExcel(Carbon::parse($payment->date)));
                $sheet->setCellValue('E' . $row, $payment->code . ' ');
                $sheet->setCellValue('F' . $row, $payment->amount);
                $sheet->setCellValue('G' . $row, $payment->amount_without_nds);
                $sheet->setCellValue('H' . $row, $payment->amount < 0 ? ($payment->organizationReceiver->name ?? '') : ($payment->organizationSender->name ?? ''));
                $sheet->setCellValue('I' . $row, $payment->description);
                $sheet->setCellValue('J' . $row, $payment->category);
                $sheet->setCellValue('K' . $row, $payment->company->name ?? '');
                $sheet->setCellValue('L' . $row, $payment->getBankName());
                $sheet->setCellValue('M' . $row, $payment->id);

                $row++;
            }
        });

        $sheet->getStyle('A1:M' . ($this->paymentCount + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:M' . ($this->paymentCount + 1));
    }

    public function columnWidths(): array
    {
        return [
            'H' => 50,
            'I' => 50,
        ];
    }
}
