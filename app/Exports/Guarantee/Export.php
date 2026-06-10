<?php

namespace App\Exports\Guarantee;

use Illuminate\Database\Eloquent\Collection;
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
    private Collection $guarantees;
    private array $total;
    private string $name;

    public function __construct(Collection $guarantees, array $total, string $name)
    {
        $this->guarantees = $guarantees;
        $this->total = $total;
        $this->name = $name;
    }

    public function title(): string
    {
        return 'Список ГУ';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A2', $this->name);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2:G2')->getFont()->setBold(true);
        $sheet->getStyle('A2:G2')->getFont()->setName('Calibri')->setSize(16);
        $sheet->getRowDimension(2)->setRowHeight(60);

        $sheet->setCellValue('A3', 'Договор');
        $sheet->setCellValue('B3', 'Заказчик');
        $sheet->setCellValue('C3', 'Сумма ГУ (по договору)');
        $sheet->setCellValue('D3', 'Сумма ГУ (по факту)');
        $sheet->setCellValue('E3', 'Оплачено ГУ');
        $sheet->setCellValue('F3', 'Остаток к получению ГУ');
        $sheet->setCellValue('G3', 'Условия оплаты гар.удержания и комментарии');

        $sheet->getRowDimension(3)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(100);

        $sheet->getStyle('A3:G3')->getFont()->setBold(true);

        $row = 4;
        foreach ($this->guarantees as $guarantee) {
            $sheet->setCellValue('A' . $row, $guarantee->contract->getName());
            $sheet->setCellValue('B' . $row, $guarantee->customer->name ?? $guarantee?->contract?->customer?->name ?? '');
            $sheet->setCellValue('C' . $row, $guarantee->amount);
            $sheet->setCellValue('D' . $row, $guarantee->fact_amount);
            $sheet->setCellValue('E' . $row, $guarantee->amount_payments);
            $sheet->setCellValue('F' . $row, $guarantee->fact_amount - $guarantee->amount_payments);
            $sheet->setCellValue('G' . $row, $guarantee->conditions);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, '');
        $sheet->setCellValue('C' . $row, $this->total['amount']['RUB']);
        $sheet->setCellValue('D' . $row, $this->total['fact_amount']['RUB']);
        $sheet->setCellValue('E' . $row, $this->total['amount_payments']['RUB']);
        $sheet->setCellValue('F' . $row, $this->total['fact_amount']['RUB'] - $this->total['amount_payments']['RUB']);
        $sheet->setCellValue('G' . $row, '');

        $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
        $sheet->getRowDimension($row)->setRowHeight(30);

        $sheet->getStyle('A3:G' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A4:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('G4:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B4:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A3:G3')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('C4:F' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');


        $sheet->setAutoFilter('A3:G' . $row);
    }
}
