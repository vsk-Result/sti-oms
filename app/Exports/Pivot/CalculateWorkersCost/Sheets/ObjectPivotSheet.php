<?php

namespace App\Exports\Pivot\CalculateWorkersCost\Sheets;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
        $lastColumnIndex = 3 + count($this->years) * 4 * 2 * 3 * 2;

        $sheet->setCellValue('A1', 'Раздел');

        $columnIndex = 2;
        foreach ($this->years as $year) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $year['name']);

            $sheet->setCellValue($column . '2', 'Сумма');
            $columnIndex++;

            $columnTwo = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($columnTwo . '2', 'Расчет по часу');
            $columnIndex++;

            $sheet->mergeCells($column . '1:' . $columnTwo . '1');

            foreach($year['quarts'] as $quart) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . '1', $quart['name']);

                $sheet->setCellValue($column . '2', 'Сумма');
                $columnIndex++;

                $columnTwo = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($columnTwo . '2', 'Расчет по часу');
                $columnIndex++;

                $sheet->mergeCells($column . '1:' . $columnTwo . '1');

                $sheet->getColumnDimension($column)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                $sheet->getColumnDimension($columnTwo)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                foreach($quart['months'] as $month) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . '1', $month['name']);

                    $sheet->setCellValue($column . '2', 'Сумма');
                    $columnIndex++;

                    $columnTwo = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($columnTwo . '2', 'Расчет по часу');
                    $columnIndex++;

                    $sheet->mergeCells($column . '1:' . $columnTwo . '1');

                    $sheet->getColumnDimension($column)->setOutlineLevel(2)
                        ->setVisible(false)
                        ->setCollapsed(true);

                    $sheet->getColumnDimension($columnTwo)->setOutlineLevel(2)
                        ->setVisible(false)
                        ->setCollapsed(true);
                }
            }
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
            foreach ($this->years as $year) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['amount'][$year['name']]['total']) ? $groupInfo['amount'][$year['name']]['total'] : '-');
                $columnIndex++;

                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['rate'][$year['name']]['total']) ? $groupInfo['rate'][$year['name']]['total'] : '-');
                $columnIndex++;

                foreach($year['quarts'] as $quart) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['amount'][$year['name']][$quart['name']]['total']) ? $groupInfo['amount'][$year['name']][$quart['name']]['total'] : '-');
                    $columnIndex++;

                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['rate'][$year['name']][$quart['name']]['total']) ? $groupInfo['rate'][$year['name']][$quart['name']]['total'] : '-');
                    $columnIndex++;

                    foreach($quart['months'] as $month) {
                        $column = $this->getColumnWord($columnIndex);
                        $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['amount'][$year['name']][$quart['name']][$month['name']]) ? $groupInfo['amount'][$year['name']][$quart['name']][$month['name']] : '-');
                        $columnIndex++;

                        $column = $this->getColumnWord($columnIndex);
                        $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($groupInfo['rate'][$year['name']][$quart['name']][$month['name']]) ? $groupInfo['rate'][$year['name']][$quart['name']][$month['name']] : '-');
                        $columnIndex++;
                    }
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
        foreach ($this->years as $year) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['amount'][$year['name']]['total']) ? $this->info['total']['amount'][$year['name']]['total'] : '-');
            $columnIndex++;

            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['rate'][$year['name']]['total']) ? $this->info['total']['rate'][$year['name']]['total'] : '-');
            $columnIndex++;

            foreach ($year['quarts'] as $quart) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['amount'][$year['name']][$quart['name']]['total']) ? $this->info['total']['amount'][$year['name']][$quart['name']]['total'] : '-');
                $columnIndex++;

                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['rate'][$year['name']][$quart['name']]['total']) ? $this->info['total']['rate'][$year['name']][$quart['name']]['total'] : '-');
                $columnIndex++;

                foreach($quart['months'] as $month) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['amount'][$year['name']][$quart['name']][$month['name']]) ? $this->info['total']['amount'][$year['name']][$quart['name']][$month['name']] : '-');
                    $columnIndex++;

                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($this->info['total']['rate'][$year['name']][$quart['name']][$month['name']]) ? $this->info['total']['rate'][$year['name']][$quart['name']][$month['name']] : '-');
                    $columnIndex++;
                }
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
        foreach ($this->years as $year) {
            $columnOne = $this->getColumnWord($columnIndex);
            $columnTwo = $this->getColumnWord($columnIndex + 1);
            $sheet->setCellValue($columnOne . $rowIndex, is_valid_amount_in_range($this->info['hours'][$year['name']]['total']) ? $this->info['hours'][$year['name']]['total'] : '-');
            $sheet->mergeCells($columnOne . $rowIndex . ':' . $columnTwo . $rowIndex);
            $columnIndex += 2;

            foreach ($year['quarts'] as $quart) {
                $columnOne = $this->getColumnWord($columnIndex);
                $columnTwo = $this->getColumnWord($columnIndex + 1);
                $sheet->setCellValue($columnOne . $rowIndex, is_valid_amount_in_range($this->info['hours'][$year['name']][$quart['name']]['total']) ? $this->info['hours'][$year['name']][$quart['name']]['total'] : '-');
                $sheet->mergeCells($columnOne . $rowIndex . ':' . $columnTwo . $rowIndex);
                $columnIndex += 2;

                foreach ($quart['months'] as $month) {
                    $columnOne = $this->getColumnWord($columnIndex);
                    $columnTwo = $this->getColumnWord($columnIndex + 1);
                    $sheet->setCellValue($columnOne . $rowIndex, is_valid_amount_in_range($this->info['hours'][$year['name']][$quart['name']][$month['name']]) ? $this->info['hours'][$year['name']][$quart['name']][$month['name']] : '-');
                    $sheet->mergeCells($columnOne . $rowIndex . ':' . $columnTwo . $rowIndex);
                    $columnIndex += 2;
                }
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
        foreach ($this->years as $year) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->getColumnDimension($column)->setWidth(18);
            $columnIndex++;

            $column = $this->getColumnWord($columnIndex);
            $sheet->getColumnDimension($column)->setWidth(18);
            $columnIndex++;

            foreach ($year['quarts'] as $quart) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->getColumnDimension($column)->setWidth(18);
                $columnIndex++;

                $column = $this->getColumnWord($columnIndex);
                $sheet->getColumnDimension($column)->setWidth(18);
                $columnIndex++;

                foreach ($quart['months'] as $month) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->getColumnDimension($column)->setWidth(18);
                    $columnIndex++;

                    $column = $this->getColumnWord($columnIndex);
                    $sheet->getColumnDimension($column)->setWidth(18);
                    $columnIndex++;
                }
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
        return Coordinate::stringFromColumnIndex($n);
    }
}
