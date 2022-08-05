<?php

namespace App\Exports\Finance\FinanceReport\Sheets;

use App\Models\PaymentImport;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private array $info;

    public function __construct(string $sheetName, array $info)
    {
        $this->sheetName = $sheetName;
        $this->info = $info;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $dateLastStatement = PaymentImport::orderByDesc('date')->first()->created_at->format('d.m.Y H:i');
        $sheet->setCellValue('A1', "Остатки на счетах" . "\n" . "(Последняя выписка загружена " . $dateLastStatement . ")");

        $row = 2;
        foreach($this->info['balances'] as $bankName => $balance) {
            if ($bankName === 'ПАО "Росбанк"') {
                continue;
            }


            $sheet->setCellValue('A' . $row, $bankName);

//            if ($bankName === 'ПАО "МКБ"') {
//                $balance = 11000;
//                $sheet->setCellValue('B' . $row, $balance);
//                $row++;
//            } else {
//                $sheet->setCellValue('B' . $row, $balance['RUB']);
//                if ($balance['EUR'] !== 0) {
//                    $row++;
//                    $sheet->setCellValue('A' . $row, $bankName);
//                    $sheet->setCellValue('B' . $row, $balance['EUR']);
//                }
//            }
//                    @else
//                        {{ \App\Models\CurrencyExchangeRate::format($balance['RUB'], 'RUB') }}
//                        @if ($balance['EUR'] !== 0)
//                            <br>
//                            {{ \App\Models\CurrencyExchangeRate::format($balance['EUR'], 'EUR') }}
//                        @endif
//                    @endif
//
//            $sheet->setCellValue('A' . $row, $bankName);
//            $sheet->setCellValue('B' . $row, 'Объект');
//
//            $row++;
        }

//        $sheet->setCellValue('A1', 'Объект');
//        $sheet->setCellValue('B1', 'Итого');
//        $sheet->setCellValue('C1', 'Сумма неоплаченных работ по актам');
//        $sheet->setCellValue('D1', 'Аванс к получению');
//        $sheet->setCellValue('E1', 'Гарантийное удержание');
//
//        $sheet->setCellValue('A2', 'Итого');
//        $sheet->setCellValue('B2', $this->pivot['total']['acts'][$this->currency] + $this->pivot['total']['avanses'][$this->currency] + $this->pivot['total']['gu'][$this->currency]);
//        $sheet->setCellValue('C2', $this->pivot['total']['acts'][$this->currency]);
//        $sheet->setCellValue('D2', $this->pivot['total']['avanses'][$this->currency]);
//        $sheet->setCellValue('E2', $this->pivot['total']['gu'][$this->currency]);
//
//        $sheet->getStyle('B2')->getFont()->setColor(new Color(($this->pivot['total']['acts'][$this->currency] + $this->pivot['total']['avanses'][$this->currency] + $this->pivot['total']['gu'][$this->currency]) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//        $sheet->getStyle('C2')->getFont()->setColor(new Color($this->pivot['total']['acts'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//        $sheet->getStyle('D2')->getFont()->setColor(new Color($this->pivot['total']['avanses'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//        $sheet->getStyle('E2')->getFont()->setColor(new Color($this->pivot['total']['gu'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//
//        $rowIndex = 3;
//        foreach ($this->pivot['entries'] as $entry) {
//            $sheet->setCellValue('A' . $rowIndex, $entry['object']['name']);
//            $sheet->setCellValue('B' . $rowIndex, $entry['acts'][$this->currency] + $entry['avanses'][$this->currency] + $entry['gu'][$this->currency]);
//            $sheet->setCellValue('C' . $rowIndex, $entry['acts'][$this->currency] === 0 ? '' : $entry['acts'][$this->currency]);
//            $sheet->setCellValue('D' . $rowIndex, $entry['avanses'][$this->currency] === 0 ? '' : $entry['avanses'][$this->currency]);
//            $sheet->setCellValue('E' . $rowIndex, $entry['gu'][$this->currency] === 0 ? '' : $entry['gu'][$this->currency]);
//
//            $sheet->getStyle('B' . $rowIndex)->getFont()->setColor(new Color(($entry['acts'][$this->currency] + $entry['avanses'][$this->currency] + $entry['gu'][$this->currency]) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//            $sheet->getStyle('C' . $rowIndex)->getFont()->setColor(new Color($entry['acts'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//            $sheet->getStyle('D' . $rowIndex)->getFont()->setColor(new Color($entry['avanses'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//            $sheet->getStyle('E' . $rowIndex)->getFont()->setColor(new Color($entry['gu'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//
//            $sheet->getRowDimension($rowIndex)->setRowHeight(20);
//            $rowIndex++;
//        }
//
//        $rowIndex--;
//
//        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
//
//        $sheet->getRowDimension(1)->setRowHeight(30);
//        $sheet->getRowDimension(2)->setRowHeight(20);
//        $sheet->getColumnDimension('A')->setWidth(40);
//        $sheet->getColumnDimension('B')->setWidth(20);
//        $sheet->getColumnDimension('C')->setWidth(40);
//        $sheet->getColumnDimension('D')->setWidth(30);
//        $sheet->getColumnDimension('E')->setWidth(30);
//
//        $sheet->getStyle('A1:B2')->getFont()->setBold(true);
//        $sheet->getStyle('B1:B' . $rowIndex)->getFont()->setBold(true);
//
//        $sheet->getStyle('B1:E1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
//        $sheet->getStyle('A1:E' . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);
//
//        $sheet->getStyle('B2:E'. $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//
//        $sheet->getStyle('A1:E'. $rowIndex)->applyFromArray([
//            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
//        ]);
//
//        $sheet->getStyle('B1:B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
//        $sheet->getStyle('A2:E2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
//
//        $sheet->setAutoFilter('A1:E' . $rowIndex);
    }
}
