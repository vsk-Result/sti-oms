<?php

namespace App\Exports\Pivot\Residence\Sheets;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    WithColumnWidths
{
    public function __construct(private array $residenceInfo, private string $date, private string $dormitoryName) {}


    public function title(): string
    {
        return 'Анализ';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6, 'B' => 9, 'C' => 35, 'D' => 20,
            'E' => 20, 'F' => 20, 'G' => 20, 'H' => 20
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue('A3', '№');
        $sheet->setCellValue('B3', 'UID');
        $sheet->setCellValue('C3', 'ФИО');
        $sheet->setCellValue('D3', 'Объект');
        $sheet->setCellValue('E3', 'Статус');
        $sheet->setCellValue('F3', 'Дата увольнения');
        $sheet->setCellValue('G3', 'Прожито дней после увольнения');
        $sheet->setCellValue('H3', 'Сумма после увольнения');
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->setCellValue('A1', 'Дата: ' . $this->date . ', общежитие: ' . $this->dormitoryName);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(20);
        $sheet->mergeCells('A1:H1');

        $row = 4;
        $counter = 1;
        foreach ($this->residenceInfo as $employee) {
            $sheet->setCellValue('A' . $row, $counter);
            $sheet->setCellValue('B' . $row, '#' . $employee['unique']);
            $sheet->setCellValue('C' . $row, $employee['name']);
            $sheet->setCellValue('D' . $row, $employee['object']);
            $sheet->setCellValue('E' . $row, $employee['status']);
            $sheet->setCellValue('F' . $row, Carbon::parse($employee['dismissal_date'])->format('d-m-Y'));
            $sheet->setCellValue('G' . $row, $employee['daysLivedIllegally']);
            $sheet->setCellValue('H' . $row, $employee['sumLivedIllegally']);
            $sheet->getRowDimension($row)->setRowHeight(30);
            $counter++;
            $row++;
        }
        $row--;

        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];
        $sheet->getStyle('A3:H' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:H' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
    }
}
