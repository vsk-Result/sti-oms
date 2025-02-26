<?php

namespace App\Exports\PaymentImport\InvalidBalance\Sheets;

use App\Models\PaymentImport;
use App\Services\PaymentImport\Type\StatementImportService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalysisSheet implements
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithHeadings,
    WithColumnWidths
{
    public function __construct(private PaymentImport $import, private StatementImportService $importService) {}

    public function title(): string
    {
        return 'Анализ оплат';
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

    public function styles(Worksheet $sheet): void
    {
        $filename = storage_path() . '/app/' . str_replace('storage/', '', $this->import->getFileLink());

        $statementData = $this->importService->getStatementDataFromExcel(new UploadedFile($filename, '1.xlsx'));
        $processInfo = $this->importService->processInfoFromStatementData($statementData);

        $diffPayments = [];

        foreach ($this->import->payments as $payment) {
            $find = false;

            foreach ($processInfo['payments'] as $i => $pay) {
                $amount = $pay['pay_amount'] == 0 ? $pay['receive_amount'] : $pay['pay_amount'];

                if ($payment->description === str_replace('  ', ' ', $pay['description']) && (float) $payment->amount == (float) $amount) {
                    $find = true;
                    break;
                }
            }

            if (! $find) {
                $diffPayments[] = $payment;
            }
        }

        $row = 2;
        foreach ($diffPayments as $payment) {
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

        $row--;

        $sheet->getStyle('A1:M' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('F2:F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $sheet->setAutoFilter('A1:M' . $row);
    }

    public function columnWidths(): array
    {
        return [
            'H' => 50,
            'I' => 50,
        ];
    }
}
