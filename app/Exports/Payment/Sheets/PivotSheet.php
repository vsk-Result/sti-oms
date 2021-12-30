<?php

namespace App\Exports\Payment\Sheets;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private Builder $payments;

    public function __construct(string $sheetName, Builder $payments)
    {
        $this->sheetName = $sheetName;
        $this->payments = $payments;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A2', 'Приход');
        $sheet->setCellValue('A3', 'Расход');
        $sheet->setCellValue('A4', 'Баланс');
        $sheet->setCellValue('B1', 'Итого');
        $sheet->setCellValue('C1', 'Итого, без НДС');

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);

        $sheet->getStyle('A4:C4')->getFont()->setBold(true);

        $sheet->getStyle('A1:C3')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A4:C4')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('B2:C2')->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('B3:C3')->getFont()->setColor(new Color(Color::COLOR_RED));

        $sheet->getStyle('A1:A4')->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('B1:C4')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);

        $sheet->getStyle('B2:C4')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->setCellValue('B2', (clone $this->payments)->where('amount', '>=', 0)->sum('amount'));
        $sheet->setCellValue('C2', (clone $this->payments)->where('amount', '>=', 0)->sum('amount_without_nds'));

        $sheet->setCellValue('B3', (clone $this->payments)->where('amount', '<', 0)->sum('amount'));
        $sheet->setCellValue('C3', (clone $this->payments)->where('amount', '<', 0)->sum('amount_without_nds'));

        $totalAmount = (clone $this->payments)->sum('amount');
        $totalAmountWithoutNDS = (clone $this->payments)->sum('amount_without_nds');

        if ($totalAmount < 0) {
            $sheet->getStyle('B4:C4')->getFont()->setColor(new Color(Color::COLOR_RED));
        } else {
            $sheet->getStyle('B4:C4')->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        }

        $sheet->setCellValue('B4', $totalAmount);
        $sheet->setCellValue('C4', $totalAmountWithoutNDS);
    }
}
