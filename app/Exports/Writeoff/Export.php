<?php

namespace App\Exports\Writeoff;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Export implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private Collection $bankGuarantees;

    public function __construct(Collection $writeoffs)
    {
        $this->writeoffs = $writeoffs;
    }

    public function title(): string
    {
        return 'Списания';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Дата');
        $sheet->setCellValue('B1', 'Объект');
        $sheet->setCellValue('C1', 'Компания');
        $sheet->setCellValue('D1', 'UID сотрудника');
        $sheet->setCellValue('E1', 'Описание');
        $sheet->setCellValue('F1', 'Сумма');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('F')->setWidth(18);

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->writeoffs as $writeoff) {
            $sheet->setCellValue('A' . $row, empty($writeoff->date) ? '' : Date::dateTimeToExcel(Carbon::parse($writeoff->date)));
            $sheet->setCellValue('B' . $row, $writeoff->object->code);
            $sheet->setCellValue('C' . $row, $writeoff->company->short_name);
            $sheet->setCellValue('D' . $row, $writeoff->crm_employee_uid);
            $sheet->setCellValue('E' . $row, $writeoff->description);
            $sheet->setCellValue('F' . $row, $writeoff->amount);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:F' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:F' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('F2:F' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('A2:A' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:F' . $row);
    }
}
