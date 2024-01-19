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

        $sheet->setCellValue('A1', 'Наименование');
        $sheet->setCellValue('B1', 'Сумма');
        $sheet->setCellValue('C1', 'Срок оплаты');
        $sheet->setCellValue('D1', 'Период');
        $sheet->setCellValue('E1', 'Платежка в 1С');
        $sheet->setCellValue('F1', 'Статус');
        $sheet->setCellValue('G1', 'Дата оплаты');

        $sheet->getRowDimension(1)->setRowHeight(25);

        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(14);
        $sheet->getColumnDimension('G')->setWidth(16);

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->items as $item) {
            $sheet->setCellValue('A' . $row, $item->name);
            $sheet->setCellValue('B' . $row, $item->amount);
            $sheet->setCellValue('C' . $row, $item->due_date ? Date::dateTimeToExcel(Carbon::parse($item->due_date)) : '');
            $sheet->setCellValue('D' . $row, $item->period);
            $sheet->setCellValue('E' . $row, $item->in_one_c ? 'Да' : 'Нет');
            $sheet->setCellValue('F' . $row,  $item->paid ? 'Оплачено' : 'Не оплачено');
            $sheet->getStyle('F' . $row)->getFont()->setColor(new Color($item->paid ? Color::COLOR_DARKGREEN : Color::COLOR_RED));
            $sheet->setCellValue('G' . $row, $item->payment_date ? Date::dateTimeToExcel(Carbon::parse($item->payment_date)) : '');

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $row--;

        $sheet->getStyle('A1:G' . $row++)->applyFromArray($THINStyleArray);

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, $this->items->where('paid', false)->sum('amount'));
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f1faff');
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('B2:B' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:G' . $row);
    }
}
