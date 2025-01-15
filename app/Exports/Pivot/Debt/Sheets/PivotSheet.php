<?php

namespace App\Exports\Pivot\Debt\Sheets;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private array $pivot;

    public function __construct(string $sheetName, array $pivot)
    {
        $this->sheetName = $sheetName;
        $this->pivot = $pivot;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->setCellValue('A1', 'Контрагент');
        $sheet->setCellValue('B1', 'Итого');
        $columnIndex = 3;
        foreach($this->pivot['objects'] as $object) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . '1', $object->getName());
            $sheet->getColumnDimension($this->getColumnWord($columnIndex))->setWidth(25);
            $columnIndex++;
        }

        $sheet->setCellValue('A2', 'Итого');
        $sheet->setCellValue('B2', array_sum($this->pivot['total']));

        $columnIndex = 3;
        foreach($this->pivot['total'] as $amount) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . '2', $amount);
            $columnIndex++;
        }

        $rowIndex = 3;
        foreach ($this->pivot['organizations'] as $organizationName => $amount) {
            $sheet->setCellValue('A' . $rowIndex, $organizationName);
            $sheet->setCellValue('B' . $rowIndex, $amount);

            $columnIndex = 3;
            foreach ($this->pivot['objects'] as $object) {
                $sheet->setCellValue($this->getColumnWord($columnIndex) . $rowIndex, $this->pivot['entries'][$organizationName][$object->id] ?? '');
                $columnIndex++;
            }

            $sheet->getRowDimension($rowIndex)->setRowHeight(20);
            $rowIndex++;
        }

        $rowIndex--;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);

        $sheet->getStyle('A1:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . '2')->getFont()->setBold(true);
        $sheet->getStyle('B1:B' . $rowIndex)->getFont()->setBold(true);

        $sheet->getStyle('B1:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . '1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);

        $sheet->getStyle('B2:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('B2:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . $rowIndex)->getFont()->setColor(new Color(Color::COLOR_RED));

        $sheet->getStyle('A1:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . $rowIndex)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('B1:B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getStyle('A2:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');

        $sheet->setAutoFilter('A1:' . $this->getColumnWord((2 + count($this->pivot['objects']))) . $rowIndex);
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
