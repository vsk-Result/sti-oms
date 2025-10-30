<?php

namespace App\Exports\Payment\Sheets;

use App\Models\KostCode;
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
            'Объект',
            'Расход/Приход',
            'Тип оплаты',
            'Дата оплаты',
            'Код затрат',
            'Статья затрат',
            'Сумма c НДС',
            'Сумма без НДС',
            'Контрагент',
            'ИНН',
            'Информация',
            'Категория',
            'Компания',
            'Банк',
            'ID оплаты в STI OMS',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $row = 2;
        $this->payments->chunk(2000, function($payments) use($sheet, &$row) {
            foreach ($payments as $payment) {
                $sheet->setCellValue('A' . $row, $payment->getObject());
                $sheet->setCellValue('B' . $row, $payment->amount < 0 ? 'Расход' : 'Приход');
                $sheet->setCellValue('C' . $row, $payment->getPaymentType());
                $sheet->setCellValue('D' . $row, Date::dateTimeToExcel(Carbon::parse($payment->date)));
                $sheet->setCellValue('E' . $row, $payment->code . ' ');
                $sheet->setCellValue('F' . $row, KostCode::getTitleByCode($payment->code));
                $sheet->setCellValue('G' . $row, $payment->amount);
                $sheet->setCellValue('H' . $row, $payment->amount_without_nds);
                $sheet->setCellValue('I' . $row, $payment->amount < 0 ? ($payment->organizationReceiver->name ?? '') : ($payment->organizationSender->name ?? ''));
                $sheet->setCellValue('J' . $row, ($payment->amount < 0 ? ($payment->organizationReceiver->inn ?? '') : ($payment->organizationSender->inn ?? '')) . ' ');
                $sheet->setCellValue('K' . $row, $payment->description);
                $sheet->setCellValue('L' . $row, $payment->category);
                $sheet->setCellValue('M' . $row, $payment->company->name ?? '');
                $sheet->setCellValue('N' . $row, $payment->getBankName());
                $sheet->setCellValue('O' . $row, $payment->id);

                $row++;
            }
        });

        $sheet->getStyle('A1:O' . ($this->paymentCount + 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('H2:H' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


        $sheet->getStyle('A1:O1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:O' . ($this->paymentCount + 1));
    }

    public function columnWidths(): array
    {
        return [
            'F' => 50,
            'I' => 50,
            'J' => 50,
        ];
    }
}
