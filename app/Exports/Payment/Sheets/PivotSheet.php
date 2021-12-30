<?php

namespace App\Exports\Payment\Sheets;

use Carbon\Carbon;
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

    private null|string $object;

    public function __construct(string $sheetName, Builder $payments, null|string $object)
    {
        $this->sheetName = $sheetName;
        $this->payments = $payments;
        $this->object = $object;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', $this->object);
        $sheet->setCellValue('B1', 'с ' . Carbon::parse((clone $this->payments)->min('date'))->format('d.m.Y'));
        $sheet->setCellValue('C1', 'по ' . Carbon::parse((clone $this->payments)->max('date'))->format('d.m.Y'));

        $sheet->setCellValue('A3', 'Приход');
        $sheet->setCellValue('A4', 'Расход');
        $sheet->setCellValue('A5', 'Баланс');
        $sheet->setCellValue('B2', 'Итого');
        $sheet->setCellValue('C2', 'Итого, без НДС');

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);

        $sheet->getStyle('A5:C5')->getFont()->setBold(true);

        $sheet->getStyle('A1:C4')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A5:C5')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('B3:C3')->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('B4:C4')->getFont()->setColor(new Color(Color::COLOR_RED));

        $sheet->getStyle('A1:A5')->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B1:C5')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);

        $sheet->getStyle('B3:C5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->setCellValue('B3', (clone $this->payments)->where('amount', '>=', 0)->sum('amount'));
        $sheet->setCellValue('C3', (clone $this->payments)->where('amount', '>=', 0)->sum('amount_without_nds'));

        $sheet->setCellValue('B4', (clone $this->payments)->where('amount', '<', 0)->sum('amount'));
        $sheet->setCellValue('C4', (clone $this->payments)->where('amount', '<', 0)->sum('amount_without_nds'));

        $totalAmount = (clone $this->payments)->sum('amount');
        $totalAmountWithoutNDS = (clone $this->payments)->sum('amount_without_nds');

        if ($totalAmount < 0) {
            $sheet->getStyle('B5:C5')->getFont()->setColor(new Color(Color::COLOR_RED));
        } else {
            $sheet->getStyle('B5:C5')->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        }

        $sheet->setCellValue('B5', $totalAmount);
        $sheet->setCellValue('C5', $totalAmountWithoutNDS);
    }
}
