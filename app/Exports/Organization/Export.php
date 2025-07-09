<?php

namespace App\Exports\Organization;

use App\Models\Organization;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Export implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    public function title(): string
    {
        return 'Контрагенты';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->setCellValue('C1', 'ИНН');
        $sheet->setCellValue('D1', 'КПП');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $row = 2;
        $organizations = Organization::orderBy('name')->get();
        foreach ($organizations as $organization) {
            $sheet->setCellValue('A' . $row,$organization->id);
            $sheet->setCellValue('B' . $row, $organization->name);
            $sheet->setCellValue('C' . $row, ' ' . $organization->inn);
            $sheet->setCellValue('D' . $row, ' ' . $organization->kpp);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:D' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:D' . $row)->getAlignment()->setVertical('center')->setWrapText(false);
    }
}
