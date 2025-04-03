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
        $lastRow = 7;
        $lastColumnIndex = 1 + count($this->periods);
        $lastColumn = $this->getColumnWord($lastColumnIndex);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getRowDimension($lastRow)->setRowHeight(50);

        $sheet->getColumnDimension('A')->setWidth(40);

        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastRow . ':' . $lastColumn . $lastRow)->getFont()->setBold(true);

        $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B2:' . $lastColumn . $lastRow)->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('B2:' . $lastColumn . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);

        $sheet->getStyle('A' . $lastRow . ':' . $lastColumn . $lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, $lastColumnIndex, $lastRow);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);

        $sheet->setCellValue('A1', 'Категория');
        $sheet->setCellValue('A2', 'Работы');
        $sheet->setCellValue('A3', 'Материалы');
        $sheet->setCellValue('A4', '    Фиксированная часть');
        $sheet->setCellValue('A5', '    Изменяемая часть');
        $sheet->setCellValue('A6', 'Накладные/Услуги');
        $sheet->setCellValue('A7', 'Итого');

        $sheet->getRowDimension(2)->setRowHeight(50);
        $sheet->getRowDimension(3)->setRowHeight(50);
        $sheet->getRowDimension(4)->setRowHeight(50);
        $sheet->getRowDimension(5)->setRowHeight(50);
        $sheet->getRowDimension(6)->setRowHeight(50);
        $sheet->getRowDimension(7)->setRowHeight(50);

        $sheet->getStyle('A4:A5')->getFont()->setItalic(true);


        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $period['format']);
            $sheet->getColumnDimension($column)->setWidth(25);
            $columnIndex++;
        }

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['contractors'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . '2', $amount);
            $columnIndex++;
        }

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = ($this->payments['providers_fix'][$period['start']] ?? 0) + ($this->payments['providers_float'][$period['start']] ?? 0);

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . '3', $amount);
            $columnIndex++;
        }

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['providers_fix'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . '4', $amount);
            $columnIndex++;
        }

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['providers_float'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . '5', $amount);
            $columnIndex++;
        }

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['service'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . '6', $amount);
            $columnIndex++;
        }

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->payments['total'][$period['start']] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . '7', $amount);
            $columnIndex++;
        }
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
