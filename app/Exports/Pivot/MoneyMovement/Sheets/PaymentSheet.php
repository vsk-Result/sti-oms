<?php

namespace App\Exports\Pivot\MoneyMovement\Sheets;

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
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $this->payments = $payments;
    }

    public function title(): string
    {
        return 'Таблица оплат';
    }

    public function headings(): array
    {
        return [
            'Объект',
            'Название объекта',
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
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $row = 2;
        $this->payments->chunk(2000, function($payments) use($sheet, &$row) {
            foreach ($payments as $payment) {
                $sheet->setCellValue('A' . $row, $payment->getObject());
                $sheet->setCellValue('B' . $row, $payment->getObjectName(),);
                $sheet->setCellValue('C' . $row, $payment->amount < 0 ? 'Расход' : 'Приход');
                $sheet->setCellValue('D' . $row, $payment->getPaymentType());
                $sheet->setCellValue('E' . $row, Date::dateTimeToExcel(Carbon::parse($payment->date)));
                $sheet->setCellValue('F' . $row, $payment->code . ' ');
                $sheet->setCellValue('G' . $row, $payment->amount);
                $sheet->setCellValue('H' . $row, $payment->amount_without_nds);
                $sheet->setCellValue('I' . $row, $payment->amount < 0 ? ($payment->organizationReceiver->name ?? '') : ($payment->organizationSender->name ?? ''));
                $sheet->setCellValue('J' . $row, $payment->description);
                $sheet->setCellValue('K' . $row, $payment->category);
                $sheet->setCellValue('L' . $row, $payment->company->name ?? '');
                $sheet->setCellValue('M' . $row, $payment->getBankName());
                $sheet->setCellValue('N' . $row, $payment->id);

                $row++;
            }
        });

        $sheet->getStyle('A1:N' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:N' . $row);
    }

    public function columnWidths(): array
    {
        return [
            'I' => 50,
            'G' => 50,
        ];
    }
}
