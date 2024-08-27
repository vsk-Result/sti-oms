<?php

namespace App\Exports\Object\ReceivePlan\Sheets;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReceivePlanSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private array $reasons;
    private array $periods;
    private Collection $plans;

    public function __construct(
        array $reasons,
        array $periods,
        Collection $plans,
    ) {
        $this->reasons = $reasons;
        $this->periods = $periods;
        $this->plans = $plans;
    }

    public function title(): string
    {
        return 'План поступлений';
    }

    public function styles(Worksheet $sheet): void
    {
        $lastRow = 2 + count($this->reasons);
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

        $sheet->setCellValue('A1', 'Основание');

        $row = 2;
        foreach ($this->reasons as $reason) {
            $sheet->setCellValue('A' . $row, $reason);
            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Итого');

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $period['format']);
            $sheet->getColumnDimension($column)->setWidth(25);
            $columnIndex++;
        }

        $row = 2;
        foreach($this->reasons as $reasonId => $reason) {

            $columnIndex = 2;
            foreach($this->periods as $period) {

                $column = $this->getColumnWord($columnIndex);
                $amount = $this->plans->where('date', $period['start'])->where('reason_id', $reasonId)->first()->amount;
                if ($amount == 0) {
                    $amount = '';
                }

                $sheet->setCellValue($column . $row, $amount);
                $columnIndex++;
            }

            $row++;
        }

        $columnIndex = 2;
        foreach($this->periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $this->plans->where('date', $period['start'])->sum('amount');
            if ($amount == 0) {
                $amount = '';
            }
            $sheet->setCellValue($column . $row, $amount);

            $columnIndex++;
        }
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
