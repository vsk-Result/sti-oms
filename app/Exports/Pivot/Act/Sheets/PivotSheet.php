<?php

namespace App\Exports\Pivot\Act\Sheets;

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

    private string $currency;

    public function __construct(string $sheetName, array $pivot, string $currency)
    {
        $this->sheetName = $sheetName;
        $this->pivot = $pivot;
        $this->currency = $currency;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Итого');
        $sheet->setCellValue('C1', 'Сумма неоплаченных работ по актам');
        $sheet->setCellValue('D1', 'Аванс к получению (измен)');
        $sheet->setCellValue('E1', 'Аванс к получению (фикс)');
        $sheet->setCellValue('F1', 'Гарантийное удержание');

        $sheet->setCellValue('A2', 'Итого');
        $sheet->setCellValue('B2', $this->pivot['total']['acts'] + $this->pivot['total']['avanses'] + $this->pivot['total']['gu']);
        $sheet->setCellValue('C2', $this->pivot['total']['acts']);
        $sheet->setCellValue('D2', $this->pivot['total']['avanses_float']);
        $sheet->setCellValue('E2', $this->pivot['total']['avanses_fix']);
        $sheet->setCellValue('F2', $this->pivot['total']['gu']);

        $sheet->getStyle('B2')->getFont()->setColor(new Color(($this->pivot['total']['acts'] + $this->pivot['total']['avanses'] + $this->pivot['total']['gu']) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('C2')->getFont()->setColor(new Color($this->pivot['total']['acts'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('D2')->getFont()->setColor(new Color($this->pivot['total']['avanses_float'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('E2')->getFont()->setColor(new Color($this->pivot['total']['avanses_fix'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('F2')->getFont()->setColor(new Color($this->pivot['total']['gu'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $rowIndex = 3;
        foreach ($this->pivot['entries'] as $entry) {
            $sheet->setCellValue('A' . $rowIndex, $entry['object']['name']);
            $sheet->setCellValue('B' . $rowIndex, $entry['acts'] + $entry['avanses'] + $entry['gu']);
            $sheet->setCellValue('C' . $rowIndex, $entry['acts'] === 0 ? '' : $entry['acts']);
            $sheet->setCellValue('D' . $rowIndex, $entry['avanses_float'] === 0 ? '' : $entry['avanses_float']);
            $sheet->setCellValue('E' . $rowIndex, $entry['avanses_fix'] === 0 ? '' : $entry['avanses_fix']);
            $sheet->setCellValue('F' . $rowIndex, $entry['gu'] === 0 ? '' : $entry['gu']);

            $sheet->getStyle('B' . $rowIndex)->getFont()->setColor(new Color(($entry['acts'] + $entry['avanses'] + $entry['gu']) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('C' . $rowIndex)->getFont()->setColor(new Color($entry['acts'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('D' . $rowIndex)->getFont()->setColor(new Color($entry['avanses_float'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('E' . $rowIndex)->getFont()->setColor(new Color($entry['avanses_fix'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('F' . $rowIndex)->getFont()->setColor(new Color($entry['gu'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $sheet->getRowDimension($rowIndex)->setRowHeight(20);
            $rowIndex++;
        }

        $rowIndex--;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->getStyle('A1:B2')->getFont()->setBold(true);
        $sheet->getStyle('B1:B' . $rowIndex)->getFont()->setBold(true);

        $sheet->getStyle('B1:F1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:F' . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);

        $sheet->getStyle('B2:F'. $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:F'. $rowIndex)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('B1:B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getStyle('A2:F2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');

        $sheet->setAutoFilter('A1:F' . $rowIndex);
    }
}
