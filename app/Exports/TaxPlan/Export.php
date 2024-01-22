<?php

namespace App\Exports\TaxPlan;

use App\Models\TaxPlanItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Export implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private Collection $items;

    public function __construct(Collection $items)
    {
        $this->items = $items;
    }

    public function title(): string
    {
        return 'План по налогам к оплате';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Компания');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->setCellValue('C1', 'Сумма');
        $sheet->setCellValue('D1', 'Срок оплаты');
        $sheet->setCellValue('E1', 'Период');
        $sheet->setCellValue('F1', 'Платежка в 1С');
        $sheet->setCellValue('G1', 'Статус');
        $sheet->setCellValue('H1', 'Дата оплаты');

        $sheet->getRowDimension(1)->setRowHeight(25);

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(16);

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->items as $item) {
            $sheet->setCellValue('A' . $row, $item->company?->short_name);
            $sheet->setCellValue('B' . $row, $item->name);
            $sheet->setCellValue('C' . $row, $item->amount);
            $sheet->setCellValue('D' . $row, $item->due_date ? Date::dateTimeToExcel(Carbon::parse($item->due_date)) : '');
            $sheet->setCellValue('E' . $row, $item->period);
            $sheet->setCellValue('F' . $row, $item->in_one_c ? 'Да' : 'Нет');
            $sheet->setCellValue('G' . $row,  $item->paid ? 'Оплачено' : 'Не оплачено');
            $sheet->getStyle('G' . $row)->getFont()->setColor(new Color($item->paid ? Color::COLOR_DARKGREEN : Color::COLOR_RED));
            $sheet->setCellValue('H' . $row, $item->payment_date ? Date::dateTimeToExcel(Carbon::parse($item->payment_date)) : '');

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $row--;

        $sheet->getStyle('A1:H' . $row++)->applyFromArray($THINStyleArray);

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->setCellValue('B' . $row, 'Итого');
        $sheet->setCellValue('C' . $row, $this->items->where('paid', false)->sum('amount'));
        $sheet->getStyle('B' . $row . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f1faff');
        $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:H' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('H2:H' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:H' . $row);
    }
}
