<?php

namespace App\Exports\Act;

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
    private Collection $acts;
    private array $total;

    public function __construct(Collection $acts, array $total)
    {
        $this->acts = $acts;
        $this->total = $total;
    }

    public function title(): string
    {
        return 'Список актов';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Договор');
        $sheet->setCellValue('B1', 'Тип');
        $sheet->setCellValue('C1', 'Номер акта');
        $sheet->setCellValue('D1', 'Отчетный период');
        $sheet->setCellValue('E1', 'Дата акта');
        $sheet->setCellValue('F1', 'Выполнено');
        $sheet->setCellValue('G1', 'Аванс удержан');
        $sheet->setCellValue('H1', 'Депозит удержан');
        $sheet->setCellValue('I1', 'К оплате');
        $sheet->setCellValue('J1', 'Дата планируемой оплаты');
        $sheet->setCellValue('K1', 'Оплачено');
        $sheet->setCellValue('L1', 'Сумма неоплаченных работ');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(35);
        $sheet->getColumnDimension('J')->setWidth(35);
        $sheet->getColumnDimension('K')->setWidth(35);
        $sheet->getColumnDimension('L')->setWidth(35);

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $row = 2;
        foreach ($this->acts as $act) {
            $sheet->setCellValue('A' . $row, $act->contract->getFullName());
            $sheet->setCellValue('B' . $row, $act->amount_avans > 0 ? 'Фиксированный' : 'Целевой');
            $sheet->setCellValue('C' . $row, $act->number);
            $sheet->setCellValue('D' . $row, $act->getPeriodFormatted());
            $sheet->setCellValue('E' . $row, $act->date ? Date::dateTimeToExcel(Carbon::parse($act->date)) : '');
            $sheet->setCellValue('F' . $row, $act->getAmount());
            $sheet->setCellValue('G' . $row, $act->getAvansAmount());
            $sheet->setCellValue('H' . $row, $act->getDepositAmount());
            $sheet->setCellValue('I' . $row, $act->getNeedPaidAmount());
            $sheet->setCellValue('J' . $row, $act->lanned_payment_date ? Date::dateTimeToExcel(Carbon::parse($act->lanned_payment_date)) : '');
            $sheet->setCellValue('K' . $row, $act->getPaidAmount());
            $sheet->setCellValue('L' . $row, $act->getLeftPaidAmount());

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, '');
        $sheet->setCellValue('D' . $row, '');
        $sheet->setCellValue('E' . $row, '');
        $sheet->setCellValue('F' . $row, $this->total['amount']['RUB']);
        $sheet->setCellValue('G' . $row, $this->total['avanses_amount']['RUB']);
        $sheet->setCellValue('H' . $row, $this->total['deposites_amount']['RUB']);
        $sheet->setCellValue('I' . $row, $this->total['need_paid_amount']['RUB']);
        $sheet->setCellValue('J' . $row, '');
        $sheet->setCellValue('K' . $row, $this->total['paid_amount']['RUB']);
        $sheet->setCellValue('L' . $row, $this->total['left_paid_amount']['RUB']);

        $sheet->getStyle('A' . $row . ':L' . $row)->getFont()->setBold(true);
        $sheet->getRowDimension($row)->setRowHeight(30);

        $sheet->getStyle('A1:L' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A2:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('B2:L' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:L1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('F2:I' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('K2:L' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
        $sheet->getStyle('J2:J' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:L' . $row);
    }
}
