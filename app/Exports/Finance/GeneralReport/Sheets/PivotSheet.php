<?php

namespace App\Exports\Finance\GeneralReport\Sheets;

use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    WithEvents
{
    private string $sheetName;
    private array $years;
    private array $items;

    public function __construct(string $sheetName, array $years, array $items)
    {
        $this->sheetName = $sheetName;
        $this->years = $years;
        $this->items = $items;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $totalAll = 0;
        $totalByYears = [];
        foreach ($this->items as $categoryItem) {
            $totalAll += $categoryItem['amount'];

            foreach ($this->years as $year) {
                if (!isset($totalByYears[$year])) {
                    $totalByYears[$year] = 0;
                }
                $totalByYears[$year] += $categoryItem['years'][$year];
            }
        }

        $columns = ['D', 'E', 'F', 'G'];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'КАТЕГОРИЯ');
        $sheet->setCellValue('B1', 'СТАТЬЯ ЗАТРАТ/ПОСТУПЛЕНИЙ');
        $sheet->setCellValue('C1', 'ИТОГО');
        $sheet->setCellValue('D1', 'Сумма');

        $columnIndex = 0;
        foreach($this->years as $year) {
            $sheet->setCellValue($columns[$columnIndex] . '2', $year);
            $columnIndex += 1;
        }

        $sheet->setCellValue('A3', 'ИТОГО');

        $sheet->setCellValue('C3', $totalAll);
        $sheet->getStyle('C3')->getFont()->setColor(new Color($totalAll < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $columnIndex = 0;
        foreach($totalByYears as $amount) {
            $sheet->setCellValue($columns[$columnIndex] . '3', $amount);
            $sheet->getStyle($columns[$columnIndex] . '3')->getFont()->setColor(new Color($amount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $columnIndex += 1;
        }

        $row = 4;
        $categoryRows = [];
        foreach($this->items as $categoryItem) {
            $totalReceive = 0;
            $totalReceiveByYear = [];
            $totalPay = 0;
            $totalPayByYear = [];

            foreach ($this->years as $year) {
                $totalReceiveByYear[$year] = 0;
                $totalPayByYear[$year] = 0;
            }


            foreach ($categoryItem['codes']['receive'] as $codeItem) {
                $totalReceive += $codeItem['amount'];

                foreach ($this->years as $year) {
                    $totalReceiveByYear[$year] += $codeItem['years'][$year];
                }
            }
            foreach ($categoryItem['codes']['pay'] as $codeItem) {
                $totalPay += $codeItem['amount'];

                foreach ($this->years as $year) {
                    $totalPayByYear[$year] += $codeItem['years'][$year];
                }
            }

            $sheet->setCellValue('A' . $row, $categoryItem['name']);
            $sheet->setCellValue('C' . $row, $categoryItem['amount']);
            $sheet->getStyle('C' . $row)->getFont()->setColor(new Color($categoryItem['amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex = 0;
            foreach($this->years as $year) {
                $sheet->setCellValue($columns[$columnIndex] . $row, $categoryItem['years'][$year] == 0 ? '' : $categoryItem['years'][$year]);
                $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($categoryItem['years'][$year] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $columnIndex += 1;
            }

            $categoryRows[] = $row;
            $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;

            $sheet->setCellValue('A' . $row, 'Приходы');
            $sheet->setCellValue('B' . $row, 'Итого');
            $sheet->setCellValue('C' . $row, $totalReceive);
            $sheet->getStyle('C' . $row)->getFont()->setColor(new Color($totalReceive < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex = 0;
            foreach($this->years as $year) {
                $sheet->setCellValue($columns[$columnIndex] . $row, $totalReceiveByYear[$year] == 0 ? '' : $totalReceiveByYear[$year]);
                $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($totalReceiveByYear[$year] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $columnIndex += 1;
            }

            $sheet->getStyle('C' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
            $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
            $sheet->getRowDimension($row)->setRowHeight(50);

            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);
            $row++;

            foreach ($categoryItem['codes']['receive'] as $codeItem) {
                $sheet->setCellValue('B' . $row, $codeItem['name']);
                $sheet->setCellValue('C' . $row, $codeItem['amount']);
                $sheet->getStyle('C' . $row)->getFont()->setColor(new Color($codeItem['amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $columnIndex = 0;
                foreach($this->years as $year) {
                    $sheet->setCellValue($columns[$columnIndex] . $row, $codeItem['years'][$year] == 0 ? '' : $codeItem['years'][$year]);
                    $columnIndex += 1;
                }

                $sheet->getRowDimension($row)->setRowHeight(50);

                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);
                $row++;
            }

            $sheet->setCellValue('A' . $row, 'Расходы');
            $sheet->setCellValue('B' . $row, 'Итого');
            $sheet->setCellValue('C' . $row, $totalPay);
            $sheet->getStyle('C' . $row)->getFont()->setColor(new Color($totalPay < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex = 0;
            foreach($this->years as $year) {
                $sheet->setCellValue($columns[$columnIndex] . $row, $totalPayByYear[$year] == 0 ? '' : $totalPayByYear[$year]);
                $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($totalPayByYear[$year] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $columnIndex += 1;
            }

            $sheet->getStyle('C' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
            $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
            $sheet->getRowDimension($row)->setRowHeight(50);

            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);
            $row++;

            foreach ($categoryItem['codes']['pay'] as $codeItem) {
                $sheet->setCellValue('B' . $row, $codeItem['name']);
                $sheet->setCellValue('C' . $row, $codeItem['amount']);
                $sheet->getStyle('C' . $row)->getFont()->setColor(new Color($codeItem['amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $columnIndex = 0;
                foreach($this->years as $year) {
                    $sheet->setCellValue($columns[$columnIndex] . $row, $codeItem['years'][$year] == 0 ? '' : $codeItem['years'][$year]);
                    $columnIndex += 1;
                }

                $sheet->getRowDimension($row)->setRowHeight(50);

                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);
                $row++;
            }
        }

        $row--;

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getRowDimension(2)->setRowHeight(50);
        $sheet->getRowDimension(3)->setRowHeight(50);

        $sheet->getColumnDimension('A')->setWidth(17);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        foreach($columns as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }

        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('A3:B3');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:G1');

        $sheet->getStyle('A1:G' . $row)->getAlignment()->setVertical('center');
        $sheet->getStyle('A1:G2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A3:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);

        $sheet->getStyle('A1:G3')->getFont()->setBold(true);
        $sheet->getStyle('C1:C' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:G' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('A3:G3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('C1:C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        foreach ($categoryRows as $r) {
            $sheet->getStyle('A' . $r . ':G' . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('dce6f1');
        }

        $sheet->getStyle('C3:G' . $row)->getNumberFormat()->setFormatCode('#,##0');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->freezePane('A3');
            },
        ];
    }
}
