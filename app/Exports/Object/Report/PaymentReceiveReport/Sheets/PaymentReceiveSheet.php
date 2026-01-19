<?php

namespace App\Exports\Object\Report\PaymentReceiveReport\Sheets;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentReceiveSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    public function __construct(private array $reportInfo) {}

    public function title(): string
    {
        return 'Отчет';
    }

    public function styles(Worksheet $sheet): void
    {
        $reportInfo = $this->reportInfo;
        $lastColumnIndex = 3 + count($reportInfo['years']) * 17;

        $sheet->setCellValue('A1', 'Отчет доходов и расходов');
        $sheet->setCellValue('A2', 'Доходная часть');
        $sheet->setCellValue('B2', 'Категория');

        $sheet->mergeCells('A1:B1');

        $columnIndex = 3;
        foreach ($reportInfo['years'] as $year) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $year['name']);

            $sheet->setCellValue($column . '2', 'Сумма');
            $columnIndex++;

            foreach($year['quarts'] as $quart) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . '1', $quart['name']);

                $sheet->setCellValue($column . '2', 'Сумма');
                $columnIndex++;

                $sheet->getColumnDimension($column)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                foreach($quart['months'] as $month) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . '1', $month['name']);

                    $sheet->setCellValue($column . '2', 'Сумма');
                    $columnIndex++;

                    $sheet->getColumnDimension($column)->setOutlineLevel(2)
                        ->setVisible(false)
                        ->setCollapsed(true);
                }
            }
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . '1', 'Накопительные');
        $sheet->setCellValue($column . '2', 'Сумма');

        $rowIndex = 3;
        $this->fillRow($sheet, $rowIndex, 'КС 2', 'Материал', $reportInfo['years'], $reportInfo['receive']['material']);
        $this->fillRow($sheet, $rowIndex, '', 'Работы', $reportInfo['years'], $reportInfo['receive']['rad']);
        $this->fillRow($sheet, $rowIndex, '', 'Накладные', $reportInfo['years'], $reportInfo['receive']['service']);
        $this->fillRow($sheet, $rowIndex, '', 'Итого доходы: ', $reportInfo['years'], $reportInfo['receive']);

        $sheet->setCellValue('A' . $rowIndex, 'Расходная часть');
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);
        $rowIndex++;

        $this->fillRow($sheet, $rowIndex, 'Подрядчики', 'Материал', $reportInfo['years'], $reportInfo['payment']['contractors']['material']);
        $this->fillRow($sheet, $rowIndex, '', 'Работы', $reportInfo['years'], $reportInfo['payment']['contractors']['rad']);
        $this->fillRow($sheet, $rowIndex, 'Поставщики', 'Материал', $reportInfo['years'], $reportInfo['payment']['providers']['material']);
        $this->fillRow($sheet, $rowIndex, 'Услуги/накладные', 'Содержание стройплащадки', $reportInfo['years'], $reportInfo['payment']['service']['service']);
        $this->fillRow($sheet, $rowIndex, 'Зарплата рабочие', '', $reportInfo['years'], $reportInfo['payment']['salary_workers']);
        $this->fillRow($sheet, $rowIndex, 'Зарплата ИТР', '', $reportInfo['years'], $reportInfo['payment']['salary_itr']);
        $this->fillRow($sheet, $rowIndex, 'Налоги с зп', '', $reportInfo['years'], $reportInfo['payment']['salary_taxes']);
        $this->fillRow($sheet, $rowIndex, 'Услуги трансфера', '', $reportInfo['years'], $reportInfo['payment']['transfer']);
        $this->fillRow($sheet, $rowIndex, 'Общие затраты (в т.ч офис)', '', $reportInfo['years'], $reportInfo['payment']['general_costs']);
        $this->fillRow($sheet, $rowIndex, 'Налоги (НДС,прибыль)', '', $reportInfo['years'], $reportInfo['payment']['accrued_taxes']);
        $this->fillRow($sheet, $rowIndex, '', 'Итого расходы: ', $reportInfo['years'], $reportInfo['payment']);

        $sheet->setCellValue('A' . $rowIndex, '');
        $sheet->setCellValue('B' . $rowIndex, 'Маржа: ');

        $columnIndex = 3;
        foreach ($reportInfo['years'] as $year) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowIndex, $reportInfo['receive'][$year['name']]['total'] + $reportInfo['payment'][$year['name']]['total']);
            $columnIndex++;

            foreach($year['quarts'] as $quart) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, $reportInfo['receive'][$year['name']][$quart['name']]['total'] + $reportInfo['payment'][$year['name']][$quart['name']]['total']);
                $columnIndex++;

                foreach($quart['months'] as $month) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, $reportInfo['receive'][$year['name']][$quart['name']][$month['name']] + $reportInfo['payment'][$year['name']][$quart['name']][$month['name']]);
                    $columnIndex++;
                }
            }
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $rowIndex, $reportInfo['receive']['total'] + $reportInfo['payment']['total']);

        $sheet->getRowDimension($rowIndex)->setRowHeight(30);

        $lastColumn = $this->getColumnWord($lastColumnIndex);
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);

        $columnIndex = 3;
        foreach ($reportInfo['years'] as $year) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->getColumnDimension($column)->setWidth(22);
            $columnIndex++;

            foreach ($year['quarts'] as $quart) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->getColumnDimension($column)->setWidth(22);
                $columnIndex++;

                foreach ($quart['months'] as $month) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->getColumnDimension($column)->setWidth(22);
                    $columnIndex++;
                }
            }
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->getColumnDimension($column)->setWidth(30);

        $sheet->getStyle('A1:' . $lastColumn . '2')->getFont()->setBold(true);
        $sheet->getStyle('A6:' . $lastColumn . '6')->getFont()->setBold(true);
        $sheet->getStyle('A' . ($rowIndex - 2) . ':' . $lastColumn . $rowIndex)->getFont()->setBold(true);

        $sheet->getStyle('A1:' . $lastColumn . '2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A3:B' . $rowIndex)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('C3:' . $lastColumn . $rowIndex)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(false);

        $sheet->getStyle('C3:' . $lastColumn . $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:' . $lastColumn . $rowIndex)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('A' . ($rowIndex - 2) . ':' . $lastColumn . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('A6:' . $lastColumn . '6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle($lastColumn . '1:' . $lastColumn . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
    }

    private function getColumnWord($n) {
        return Coordinate::stringFromColumnIndex($n);
    }

    public function fillRow(&$sheet, &$rowIndex, $titleOne, $titleTwo, $years, $value): void
    {
        $sheet->setCellValue('A' . $rowIndex, $titleOne);
        $sheet->setCellValue('B' . $rowIndex, $titleTwo);

        $columnIndex = 3;
        foreach ($years as $year) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($value[$year['name']]['total']) ? $value[$year['name']]['total'] : '-');
            $columnIndex++;

            foreach($year['quarts'] as $quart) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($value[$year['name']][$quart['name']]['total']) ? $value[$year['name']][$quart['name']]['total'] : '-');
                $columnIndex++;

                foreach($quart['months'] as $month) {
                    $column = $this->getColumnWord($columnIndex);
                    $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($value[$year['name']][$quart['name']][$month['name']]) ? $value[$year['name']][$quart['name']][$month['name']] : '-');
                    $columnIndex++;
                }
            }
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $rowIndex, is_valid_amount_in_range($value['total']) ? $value['total'] : '-');

        $sheet->getRowDimension($rowIndex)->setRowHeight(30);
        $rowIndex++;
    }
}
