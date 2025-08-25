<?php

namespace App\Exports\Pivot\CalculateWorkersCost\Sheets;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObjectPivotSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private array $info;
    private array $years;

    public function __construct(string $sheetName, array $info, array $years)
    {
        $this->sheetName = $sheetName;
        $this->info = $info;
        $this->years = $years;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $lastColumnIndex = 3 + count($this->years) * 8;

        $sheet->setCellValue('A1', 'Раздел');

        $columnIndex = 2;
        foreach ($this->years as $year => $quarts) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $year);
            $yearColumn = $column;

            foreach ($quarts as $quart => $dates) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . '2', $quart);
                $columnIndex++;

                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . '2', 'Расчет по часу');
                $columnIndex++;
            }

            $sheet->mergeCells($yearColumn . '1:' . $column . '1');
        }

        $columnTotalOne = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($columnTotalOne . '1', 'Итого');
        $sheet->setCellValue($columnTotalOne . '2', 'Сумма');
        $columnIndex++;

        $columnTotalTwo = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($columnTotalTwo . '2', 'Расчет по часу');

        $sheet->mergeCells($columnTotalOne . '1:' . $columnTotalTwo . '1');


        $rowIndex = 3;
        foreach ($this->info['data'] as $group => $groupInfo) {
            if (str_starts_with($group, '- ')) {
                $sheet->setCellValue('A' . $rowIndex, '    ' . $group);
            } else {
                $sheet->setCellValue('A' . $rowIndex, $group);
            }

            $columnIndex = 2;
            foreach ($this->years as $year => $quarts) {
                foreach ($quarts as $quart => $dates) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['amount'][$year][$quart]) ? $groupInfo['amount'][$year][$quart] : '-');
                    $columnIndex++;

                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['rate'][$year][$quart]) ? $groupInfo['rate'][$year][$quart] : '-');
                    $columnIndex++;
                }
            }

            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['total']['amount']['total']) ? $groupInfo['total']['amount']['total'] : '-');
            $columnIndex++;

            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['total']['rate']['total']) ? $groupInfo['total']['rate']['total'] : '-');

            $sheet->getRowDimension($rowIndex)->setRowHeight(30);
            $rowIndex++;
        }


        $sheet->setCellValue('A' . $rowIndex, 'Итого');
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);

        $columnIndex = 2;
        foreach ($this->years as $year => $quarts) {
            foreach ($quarts as $quart => $dates) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['amount'][$year][$quart]) ? $this->info['total']['amount'][$year][$quart] : '-');
                $columnIndex++;

                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['rate'][$year][$quart]) ? $this->info['total']['rate'][$year][$quart] : '-');
                $columnIndex++;
            }
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['amount']['total']) ? $this->info['total']['amount']['total'] : '-');
        $columnIndex++;

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['rate']['total']) ? $this->info['total']['rate']['total'] : '-');

        $sheet->getRowDimension($rowIndex)->setRowHeight(30);
        $rowIndex++;


        $sheet->setCellValue('A' . $rowIndex, 'Количество часов рабочих (по данным из CRM)');
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);

        $columnIndex = 2;
        foreach ($this->years as $year => $quarts) {
            foreach ($quarts as $quart => $dates) {
                $columnOne = $this->getColumnWord($columnIndex);
                $columnTwo = $this->getColumnWord($columnIndex + 1);
                $sheet->setCellValue($columnOne . $rowIndex, is_valid_amount_in_range($this->info['hours'][$year][$quart]) ? $this->info['hours'][$year][$quart] : '-');
                $sheet->mergeCells($columnOne . $rowIndex . ':' . $columnTwo . $rowIndex);
                $columnIndex += 2;
            }
        }

        $columnOne = $this->getColumnWord($columnIndex);
        $columnTwo = $this->getColumnWord($columnIndex + 1);
        $sheet->setCellValue($columnOne . $rowIndex, is_valid_amount_in_range($this->info['total']['hours']['total']) ? $this->info['total']['hours']['total'] : '-');
        $sheet->mergeCells($columnOne . $rowIndex . ':' . $columnTwo . $rowIndex);
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);



        $lastColumn = $this->getColumnWord($lastColumnIndex);
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getColumnDimension('A')->setWidth(80);

        $columnIndex = 2;
        foreach ($this->years as $quarts) {
            foreach ($quarts as $dates) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->getColumnDimension($column)->setWidth(18);
                $columnIndex++;

                $column = $this->getColumnWord($columnIndex);
                $sheet->getColumnDimension($column)->setWidth(18);
                $columnIndex++;
            }
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->getColumnDimension($column)->setWidth(30);
        $columnIndex++;

        $column = $this->getColumnWord($columnIndex);
        $sheet->getColumnDimension($column)->setWidth(30);

        $sheet->getStyle('A1:' . $lastColumn . '2')->getFont()->setBold(true);
        $sheet->getStyle('A' . ($rowIndex - 1) . ':' . $lastColumn . $rowIndex)->getFont()->setBold(true);

        $sheet->getStyle('A1:' . $lastColumn . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);
        $sheet->getStyle('B3:' . $lastColumn . $rowIndex)->getAlignment()->setHorizontal('right')->setWrapText(false);
        $sheet->getStyle('A1:' . $lastColumn . '2')->getAlignment()->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('B' . $rowIndex . ':' . $lastColumn . $rowIndex)->getAlignment()->setHorizontal('center')->setWrapText(false);

        $sheet->getStyle('B3:' . $lastColumn . $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:' . $lastColumn . $rowIndex)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('A' . ($rowIndex - 1) . ':' . $lastColumn . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
    }

    public function formatAmount($amount)
    {
        return $amount == 0 ? '-' : $amount;
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
