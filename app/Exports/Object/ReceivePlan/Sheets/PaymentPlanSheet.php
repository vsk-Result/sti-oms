<?php

namespace App\Exports\Object\ReceivePlan\Sheets;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentPlanSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private array $payments;
    private array $periods;

    public function __construct(
        array $periods,
        array $payments,
    ) {
        $this->payments = $payments;
        $this->periods = $periods;
    }

    public function title(): string
    {
        return 'План расходов';
    }

    public function styles(Worksheet $sheet): void
    {
        $lastColumnIndex = 2 + count($this->periods);
        $lastColumn = $this->getColumnWord($lastColumnIndex);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);
        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(32);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->setCellValue('A1', 'Основание');
        $sheet->setCellValue('B1', 'Не оплачено с прошлого периода');

        $row = 1;
        $columnIndex = 3;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $row, $period['format']);
            $sheet->getColumnDimension($column)->setWidth(25);
            $columnIndex++;
        }

        $row++;
        $sheet->setCellValue('A' . $row, 'Работы');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $column = $this->getColumnWord(2);
        $amount = $this->payments['contractors']['no_paid'] ?? 0;

        if ($amount == 0) {
            $amount = '';
        }

        $sheet->setCellValue($column . $row, $amount);

        $columnIndex = 3;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['contractors'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $row++;
        foreach($this->payments['details']['contractors'] as $contractorName => $info) {
            $sheet->setCellValue('A' . $row, '        ' . $contractorName);
            $sheet->getRowDimension($row)->setRowHeight(50);

            $column = $this->getColumnWord(2);
            $amount = $info['no_paid'] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);

            $columnIndex = 3;
            foreach($this->periods as $period) {

                $column = $this->getColumnWord($columnIndex);
                $amount = $info[$period['start']] ?? 0;

                if ($amount == 0) {
                    $amount = '';
                }

                $sheet->setCellValue($column . $row, $amount);
                $columnIndex++;
            }

            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setItalic(true);
            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setName('Calibri')->setSize(10);

            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);

            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Материалы');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $column = $this->getColumnWord(2);
        $amount = ($this->payments['providers_fix']['no_paid'] ?? 0) + ($this->payments['providers_float']['no_paid'] ?? 0);

        if ($amount == 0) {
            $amount = '';
        }

        $sheet->setCellValue($column . $row, $amount);

        $columnIndex = 3;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = ($this->payments['providers_fix'][$period['start']] ?? 0) + ($this->payments['providers_float'][$period['start']] ?? 0);

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $row++;
        $sheet->setCellValue('A' . $row, '    Фиксированная часть');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $column = $this->getColumnWord(2);
        $amount = $this->payments['providers_fix']['no_paid'] ?? 0;

        if ($amount == 0) {
            $amount = '';
        }

        $sheet->setCellValue($column . $row, $amount);

        $columnIndex = 3;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['providers_fix'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $row++;
        foreach($this->payments['details']['providers_fix'] as $contractorName => $info) {
            $sheet->setCellValue('A' . $row, '            ' . $contractorName);
            $sheet->getRowDimension($row)->setRowHeight(50);

            $column = $this->getColumnWord(2);
            $amount = $info['no_paid'] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);

            $columnIndex = 3;
            foreach($this->periods as $period) {

                $column = $this->getColumnWord($columnIndex);
                $amount = $info[$period['start']] ?? 0;

                if ($amount == 0) {
                    $amount = '';
                }

                $sheet->setCellValue($column . $row, $amount);
                $columnIndex++;
            }

            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setItalic(true);
            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setName('Calibri')->setSize(10);

            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);

            $row++;
        }

        $sheet->setCellValue('A' . $row, '    Изменяемая часть');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $column = $this->getColumnWord(2);
        $amount = $this->payments['providers_float']['no_paid'] ?? 0;

        if ($amount == 0) {
            $amount = '';
        }

        $sheet->setCellValue($column . $row, $amount);

        $columnIndex = 3;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['providers_float'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $row++;
        foreach($this->payments['details']['providers_float'] as $contractorName => $info) {
            $sheet->setCellValue('A' . $row, '            ' . $contractorName);
            $sheet->getRowDimension($row)->setRowHeight(50);

            $column = $this->getColumnWord(2);
            $amount = $info['no_paid'] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);

            $columnIndex = 3;
            foreach($this->periods as $period) {

                $column = $this->getColumnWord($columnIndex);
                $amount = $info[$period['start']] ?? 0;

                if ($amount == 0) {
                    $amount = '';
                }

                $sheet->setCellValue($column . $row, $amount);
                $columnIndex++;
            }

            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setItalic(true);
            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setName('Calibri')->setSize(10);

            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);

            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Накладные/Услуги');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $column = $this->getColumnWord(2);
        $amount = $this->payments['service']['no_paid'] ?? 0;

        if ($amount == 0) {
            $amount = '';
        }

        $sheet->setCellValue($column . $row, $amount);

        $columnIndex = 3;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['service'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $row++;
        foreach($this->payments['details']['service'] as $contractorName => $info) {
            $sheet->setCellValue('A' . $row, '        ' . $contractorName);
            $sheet->getRowDimension($row)->setRowHeight(50);

            $column = $this->getColumnWord(2);
            $amount = $info['no_paid'] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);

            $columnIndex = 3;
            foreach($this->periods as $period) {

                $column = $this->getColumnWord($columnIndex);
                $amount = $info[$period['start']] ?? 0;

                if ($amount == 0) {
                    $amount = '';
                }

                $sheet->setCellValue($column . $row, $amount);
                $columnIndex++;
            }

            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setItalic(true);
            $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setName('Calibri')->setSize(10);

            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);

            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $column = $this->getColumnWord(2);
        $amount = $this->payments['total']['no_paid'] ?? 0;

        if ($amount == 0) {
            $amount = '';
        }

        $sheet->setCellValue($column . $row, $amount);

        $columnIndex = 3;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['total'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
        $sheet->getStyle('A2:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B2:' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('B2:' . $lastColumn . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A1:' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, $lastColumnIndex, $row);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
