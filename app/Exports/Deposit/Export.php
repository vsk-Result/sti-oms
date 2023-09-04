<?php

namespace App\Exports\Deposit;

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
    private Collection $deposits;

    public function __construct(Collection $deposits)
    {
        $this->deposits = $deposits;
    }

    public function title(): string
    {
        return 'Депозиты';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Договор');
        $sheet->setCellValue('C1', 'Валюта');
        $sheet->setCellValue('D1', 'Контрагент');
        $sheet->setCellValue('E1', 'Дата начала');
        $sheet->setCellValue('F1', 'Дата окончания');
        $sheet->setCellValue('G1', 'Сумма');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(17);

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->deposits as $deposit) {
            $sheet->setCellValue('A' . $row, $deposit->object->code);
            $sheet->setCellValue('B' . $row, $deposit->contract?->getName());
            $sheet->setCellValue('C' . $row, $deposit->currency);
            $sheet->setCellValue('D' . $row, $deposit->organization?->name);
            $sheet->setCellValue('E' . $row, Date::dateTimeToExcel(Carbon::parse($deposit->start_date)));
            $sheet->setCellValue('F' . $row, Date::dateTimeToExcel(Carbon::parse($deposit->end_date)));
            $sheet->setCellValue('G' . $row, $deposit->amount);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:G' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('E2:F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:G' . $row);
    }
}
