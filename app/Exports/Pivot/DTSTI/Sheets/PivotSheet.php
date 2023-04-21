<?php

namespace App\Exports\Pivot\DTSTI\Sheets;

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

    private array $pivot;

    public function __construct(string $sheetName, array $pivot)
    {
        $this->sheetName = $sheetName;
        $this->pivot = $pivot;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Сумма долга');
        $sheet->setCellValue('C1', 'Договор СТИ');
        $sheet->setCellValue('D1', 'Договор ДТ');

        $sheet->setCellValue('A2', 'Итого');
        $sheet->setCellValue('B2', $this->pivot['total']);

        $sheet->getStyle('B2')->getFont()->setColor(new Color($this->pivot['total'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $rowIndex = 3;
        foreach ($this->pivot['entries'] as $entry) {
            $sheet->setCellValue('A' . $rowIndex, $entry['object']['name']);
            $sheet->setCellValue('B' . $rowIndex, $entry['amount']);
            $sheet->setCellValue('C' . $rowIndex, $entry['sti']);
            $sheet->setCellValue('D' . $rowIndex, $entry['dt']);

            $sheet->getStyle('B' . $rowIndex)->getFont()->setColor(new Color($entry['amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $sheet->getRowDimension($rowIndex)->setRowHeight(20);
            $rowIndex++;
        }

        $rowIndex--;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(60);
        $sheet->getColumnDimension('D')->setWidth(60);

        $sheet->getStyle('A1:D2')->getFont()->setBold(true);
        $sheet->getStyle('B1:B' . $rowIndex)->getFont()->setBold(true);

        $sheet->getStyle('A1:D1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:D' . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);
        $sheet->getStyle('C3:D' . $rowIndex)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(false);

        $sheet->getStyle('B2:D'. $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:D'. $rowIndex)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('B1:B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getStyle('A2:D2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');

        $sheet->setAutoFilter('A1:D' . $rowIndex);
    }
}
