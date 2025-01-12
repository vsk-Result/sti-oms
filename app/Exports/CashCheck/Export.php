<?php

namespace App\Exports\CashCheck;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Export implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private array $details;

    private string $title;

    public function __construct(array $details, string $title)
    {
        $this->details = $details;
        $this->title = $title;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Дата');
        $sheet->setCellValue('B1', 'Объект');
        $sheet->setCellValue('C1', 'Статья затрат');
        $sheet->setCellValue('D1', 'Контрагент');
        $sheet->setCellValue('E1', 'Сумма');
        $sheet->setCellValue('F1', 'Категория');
        $sheet->setCellValue('G1', 'Описание');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(70);

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->details as $detail) {
            $sheet->setCellValue('A' . $row, Date::dateTimeToExcel(Carbon::parse($detail['date'])));
            $sheet->setCellValue('B' . $row, $detail['object_code'] . ' - ' . $detail['object_name']);
            $sheet->setCellValue('C' . $row, $detail['code']);
            $sheet->setCellValue('D' . $row, $detail['organization']);
            $sheet->setCellValue('E' . $row, $detail['amount']);
            $sheet->setCellValue('F' . $row, $detail['category']);
            $sheet->setCellValue('G' . $row, $detail['description']);

            $sheet->getStyle('E' . $row)->getFont()->setColor(new Color($detail['amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));


            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:G' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('D2:D' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('G2:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('A2:A' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:G' . $row);
    }
}
