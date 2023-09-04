<?php

namespace App\Exports\z;

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

    public function __construct(Collection $bankGuarantees)
    {
        $this->bankGuarantees = $bankGuarantees;
    }

    public function title(): string
    {
        return 'БГ и депозиты';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Договор');
        $sheet->setCellValue('C1', 'Номер');
        $sheet->setCellValue('D1', 'Валюта');
        $sheet->setCellValue('E1', 'Контрагент');
        $sheet->setCellValue('F1', 'Дата окончания БГ');
        $sheet->setCellValue('G1', 'Сумма БГ');
        $sheet->setCellValue('H1', 'Комиссия БГ');
        $sheet->setCellValue('I1', 'Остаток неотр. аванса');
        $sheet->setCellValue('J1', 'Дата окончания депозита');
        $sheet->setCellValue('K1', 'Сумма депозита');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getColumnDimension('H')->setWidth(17);
        $sheet->getColumnDimension('I')->setWidth(26);
        $sheet->getColumnDimension('J')->setWidth(29);
        $sheet->getColumnDimension('K')->setWidth(21);

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->bankGuarantees as $guarantee) {
            $sheet->setCellValue('A' . $row, $guarantee->object->code);
            $sheet->setCellValue('B' . $row, $guarantee->contract?->getName());
            $sheet->setCellValue('C' . $row, $guarantee->number);
            $sheet->setCellValue('D' . $row, $guarantee->currency);
            $sheet->setCellValue('E' . $row, $guarantee->organization?->name);
            $sheet->setCellValue('F' . $row, Date::dateTimeToExcel(Carbon::parse($guarantee->end_date)));
            $sheet->setCellValue('G' . $row, $guarantee->amount);
            $sheet->setCellValue('H' . $row, $guarantee->commission);
            $sheet->setCellValue('I' . $row, $guarantee->getAvansesLeftAmount());
            $sheet->setCellValue('J' . $row, Date::dateTimeToExcel(Carbon::parse($guarantee->end_date_deposit)));
            $sheet->setCellValue('K' . $row, $guarantee->amount_deposit);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:K' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:K' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('G2:I' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('K2:K' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('F2:F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('J2:J' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:K' . $row);
    }
}
