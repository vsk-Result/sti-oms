<?php

namespace App\Exports\Pivot\MoneyMovement\Sheets;

use App\Models\Object\BObject;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotObjectSheet implements
    WithTitle,
    WithStyles
{
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $this->payments = $payments;
    }

    public function title(): string
    {
        return 'Сводная по объектам';
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Период с ' . Carbon::parse((clone $this->payments)->min('date'))->format('d.m.Y') . ' по ' . Carbon::parse((clone $this->payments)->max('date'))->format('d.m.Y'));

        $sheet->setCellValue('A2', 'Объект');
        $sheet->setCellValue('B2', 'Приход');
        $sheet->setCellValue('C2', 'Расход');
        $sheet->setCellValue('D2', 'Сальдо');

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);

        $sheet->getStyle('A1:D2')->getFont()->setBold(true);
        $sheet->mergeCells('A1:D1');

        $objectIds = array_unique((clone $this->payments)->pluck('object_id')->toArray());
        $objects = BObject::whereIn('id', $objectIds);

        $row = 3;
        foreach ($objects as $object) {
            $total = (clone $this->payments)->where('object_id', $object->id)->sum('amount');

            $sheet->setCellValue('A' . $row, $object->getName());
            $sheet->setCellValue('B' . $row, (clone $this->payments)->where('object_id', $object->id)->where('amount', '>=', 0)->sum('amount'));
            $sheet->setCellValue('C' . $row, (clone $this->payments)->where('object_id', $object->id)->where('amount', '<', 0)->sum('amount'));
            $sheet->setCellValue('D' . $row, $total);

            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
            $sheet->getStyle('C' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
            $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($total < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $row++;
        }

        $total = (clone $this->payments)->sum('amount');
        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, (clone $this->payments)->where('amount', '>=', 0)->sum('amount'));
        $sheet->setCellValue('C' . $row, (clone $this->payments)->where('amount', '<', 0)->sum('amount'));
        $sheet->setCellValue('D' . $row, $total);

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('C' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($total < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $sheet->getStyle('A1:D' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:D' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A2:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);

        $sheet->getStyle('B3:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }
}
