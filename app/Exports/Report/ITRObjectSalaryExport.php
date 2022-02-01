<?php

namespace App\Exports\Report;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ITRObjectSalaryExport implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private array $objects;

    private array $data;

    public function __construct(array $objects, array $data)
    {
        $this->objects = $objects;
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Свод(проект+месяц)';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];
        $THICKStyleArray = [ 'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Проект');
        $sheet->setCellValue('B1', 'Код');

        $sheet->getRowDimension(1)->setRowHeight(35);
        $sheet->getRowDimension(2)->setRowHeight(40);

        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(13);

        $sheet->getStyle('A1:B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('dbeef4');
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        $row = 3;
        foreach ($this->objects as $object) {
            $sheet->setCellValue('B' . $row, $object);
            $sheet->getStyle('B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
            $row++;
        }

        $totals = [];
        $columnIndex = 3;
        foreach ($this->data as $month => $data) {
            $row = 1;

            $headerRange = Coordinate::stringFromColumnIndex($columnIndex) . $row . ':' . Coordinate::stringFromColumnIndex($columnIndex + 3) . $row;
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . $row, $month);
            $sheet->mergeCells($headerRange);
            $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('dbeef4');
            $sheet->getStyle($headerRange)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $row++;

            $headerRange = Coordinate::stringFromColumnIndex($columnIndex) . $row . ':' . Coordinate::stringFromColumnIndex($columnIndex + 3) . $row;
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . $row, '%');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex + 1) . $row, 'зарплата');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex + 2) . $row, 'НДФЛ+ЕСН');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex + 3) . $row, 'НДС');
            $sheet->getStyle($headerRange)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
            $sheet->getStyle($headerRange)->getFont()->setBold(true);

            $row = 3;
            $totalPercent = 0;
            $totalSalary = 0;
            $totalTax = 0;
            $totalNDS = 0;
            foreach ($data as $info) {
                $sheet->setCellValueByColumnAndRow($columnIndex, $row, $info['percent']);
                $sheet->setCellValueByColumnAndRow($columnIndex + 1, $row, $info['salary']);
                $sheet->setCellValueByColumnAndRow($columnIndex + 2, $row, $info['tax']);
                $sheet->setCellValueByColumnAndRow($columnIndex + 3, $row, $info['nds']);

                $totalPercent += $info['percent'];
                $totalSalary += $info['salary'];
                $totalTax += $info['tax'];
                $totalNDS += $info['nds'];

                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setWidth(13);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex + 1))->setWidth(13);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex + 2))->setWidth(13);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex + 3))->setWidth(13);

                $row++;
            }

            $totals[$month]['percent'] = $totalPercent;
            $totals[$month]['salary'] = $totalSalary;
            $totals[$month]['tax'] = $totalTax;
            $totals[$month]['nds'] = $totalNDS;

            $columnIndex += 4;
        }

        $sheet->getStyle('A1:' . Coordinate::stringFromColumnIndex($columnIndex - 1) . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('C3:' . Coordinate::stringFromColumnIndex($columnIndex - 1) . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('C3:' . Coordinate::stringFromColumnIndex($columnIndex - 1) . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(false);

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->getStyle('A1:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->mergeCells("A$row:B$row");

        $columnIndex = 3;
        foreach ($totals as $month => $data) {
            $sheet->setCellValueByColumnAndRow($columnIndex, $row, $data['percent']);
            $sheet->setCellValueByColumnAndRow($columnIndex + 1, $row, $data['salary']);
            $sheet->setCellValueByColumnAndRow($columnIndex + 2, $row, $data['tax']);
            $sheet->setCellValueByColumnAndRow($columnIndex + 3, $row, $data['nds']);

            $sheet->getStyle(Coordinate::stringFromColumnIndex($columnIndex) . '1:' . Coordinate::stringFromColumnIndex($columnIndex + 3) . $row)->applyFromArray($THICKStyleArray);

            $columnIndex += 4;
        }

        $sheet->getRowDimension($row)->setRowHeight(35);
        $sheet->getStyle('A' . $row . ':' . Coordinate::stringFromColumnIndex($columnIndex - 1) . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('dbeef4');
        $sheet->getStyle('A' . $row . ':' . Coordinate::stringFromColumnIndex($columnIndex - 1) . $row)->getFont()->setBold(true);
    }
}
