<?php

namespace App\Exports\Loan;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Export implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private Collection $loans;

    public function __construct(Collection $loans)
    {
        $this->loans = $loans;
    }

    public function title(): string
    {
        return 'Займы и кредиты';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Тип');
        $sheet->setCellValue('B1', 'Банк');
        $sheet->setCellValue('C1', 'Кредитор/Займодавец');
        $sheet->setCellValue('D1', 'Заемщик');
        $sheet->setCellValue('E1', 'Номер');
        $sheet->setCellValue('F1', 'Дата зачисления');
        $sheet->setCellValue('G1', 'Дата окончания');
        $sheet->setCellValue('H1', 'Сумма займа/кредита');
        $sheet->setCellValue('I1', 'Сумма погашено/доступно');
        $sheet->setCellValue('J1', 'Сумма долга');
        $sheet->setCellValue('K1', 'Проценты');
        $sheet->setCellValue('L1', 'Описание');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(22);
        $sheet->getColumnDimension('H')->setWidth(22);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(22);
        $sheet->getColumnDimension('K')->setWidth(22);
        $sheet->getColumnDimension('L')->setWidth(100);

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->loans as $loan) {
            $sheet->setCellValue('A' . $row, $loan->getType() . $loan->isCredit() ? (' (' . $loan->getCreditType() . ')') : '');
            $sheet->setCellValue('B' . $row, $loan->getBankName());
            $sheet->setCellValue('C' . $row, $loan->isLender() ? $loan->organization?->name : $loan->company?->name);
            $sheet->setCellValue('D' . $row, $loan->isLender() ? $loan->company?->name : $loan->organization?->name);
            $sheet->setCellValue('E' . $row, $loan->name);
            $sheet->setCellValue('F' . $row, $loan->start_date);
            $sheet->setCellValue('G' . $row, $loan->end_date);
            $sheet->setCellValue('H' . $row, $loan->total_amount);
            $sheet->setCellValue('I' . $row, $loan->isCredit() && !$loan->isDefaultCredit() ? ($loan->total_amount - $loan->getPaidAmount()) : $loan->getPaidAmount());
            $sheet->setCellValue('J' . $row, $loan->amount);
            $sheet->setCellValue('K' . $row, $loan->percent);
            $sheet->setCellValue('L' . $row, $loan->description);

            $row++;
        }

        $totalP = 0;
        foreach ($this->loans as $loan) {
            if ($loan->isCredit() && !$loan->isDefaultCredit()) {
                $totalP += $loan->total_amount - $loan->getPaidAmount();
            } else {
                $totalP += $loan->getPaidAmount();
            }
        }

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('H' . $row, $this->loans->sum('total_amount'));
        $sheet->setCellValue('I' . $row, $totalP);
        $sheet->setCellValue('J' . $row, $this->loans->sum('amount'));

        $sheet->getStyle('A' . $row . ':L' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getRowDimension($row)->setRowHeight(30);


        $sheet->getStyle('A1:L' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:L' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('L2:L' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('H2:J' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('F2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:L' . $row);

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, 12, $row);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }
}
