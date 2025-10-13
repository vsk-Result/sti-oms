<?php

namespace App\Exports\AccruedTax\Sheets;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private array $names;
    private Collection $taxes;
    private array $dates;

    public function __construct(array $names, Collection $taxes, array $dates)
    {
        $this->names = $names;
        $this->taxes = $taxes;
        $this->dates = $dates;
    }

    public function title(): string
    {
        return 'Начисленные налоги';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $columnIndex = 2;
        foreach($this->dates as $year => $months) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $year);
            $columnIndex += 12;
            $Ncolumn = $this->getColumnWord($columnIndex - 1);
            $sheet->mergeCells($column . '1:' . $Ncolumn . '1');
        }

        $columnIndex = 2;
        foreach($this->dates as $year => $months) {
            foreach($months as $month) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . '2', $month['month']);
                $columnIndex++;
            }
        }

        $row = 3;
        foreach($this->names as $name) {
            $sheet->setCellValue('A' . $row, $name);

            $sheet->getRowDimension($row)->setRowHeight(30);

            $columnIndex = 2;
            foreach($this->dates as $year => $months) {
                foreach($months as $month) {
                    $tax = $this->taxes->where('date', $month['date'])->where('name', $name)->first();
                    $amount = $tax ? $tax->amount : 0;

                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $row, $this->formatAmount($amount));
                    $columnIndex++;
                }
            }

            $row++;
        }

        $row--;

        $sheet->getStyle('A1:' . $column . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:' . $column . '2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A1:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B3:' . $column . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('B3:' . $column . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }

    public function formatAmount($amount)
    {
        if (empty($amount) || ! is_valid_amount_in_range($amount)) {
            return '-';
        }

        return $amount;
    }

    private function getColumnWord($n) {
        return Coordinate::stringFromColumnIndex($n);
    }
}
