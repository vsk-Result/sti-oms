<?php

namespace App\Exports\Payment\Sheets;

use App\Models\KostCode;
use App\Models\Object\BObject;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryPivot implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private Builder $payments;

    public function __construct(string $sheetName, Builder $payments)
    {
        $this->sheetName = $sheetName;
        $this->payments = $payments;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Категория/Статья затрат');
        $sheet->setCellValue('B1', 'Приходы');
        $sheet->setCellValue('C1', 'Расходы');
        $sheet->setCellValue('D1', 'Общая сумма');

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(65);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);

        $lastColumn = 'D';

        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);

        $row = 2;
        $groupedByCategoryPayments = (clone $this->payments)->get()->sortBy('category')->groupBy('category');

        $sumPay = 0;
        $sumReceive = 0;

        foreach ($groupedByCategoryPayments as $category => $categoryPayments) {

            $total = $categoryPayments->sum('amount');

            $receive = $categoryPayments->where('amount', '>=', 0)->sum('amount');
            $pay = $categoryPayments->where('amount', '<', 0)->sum('amount');

            $sheet->setCellValue('A' . $row, $category);
            $sheet->setCellValue('B' . $row, $pay);
            $sheet->setCellValue('C' . $row, $receive);
            $sheet->setCellValue('D' . $row, $total);

            $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($total < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getRowDimension($row)->setRowHeight(30);
            $row++;

            $sumPay += $pay;
            $sumReceive += $receive;

            $groupedByCodePayments = $categoryPayments->sortBy('code')->groupBy('code');

            foreach ($groupedByCodePayments as $code => $codePayments) {
                $total = $codePayments->sum('amount');

                $receive = $codePayments->where('amount', '>=', 0)->sum('amount');
                $pay = $codePayments->where('amount', '<', 0)->sum('amount');

                $sheet->setCellValue('A' . $row, '    ' . KostCode::getTitleByCode($code));
                $sheet->setCellValue('B' . $row, $pay);
                $sheet->setCellValue('C' . $row, $receive);
                $sheet->setCellValue('D' . $row, $total);

                $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($total < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $sheet->getRowDimension($row)->setRowHeight(30);
                $row++;
            }
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, $sumReceive);
        $sheet->setCellValue('C' . $row, $sumPay);
        $sheet->setCellValue('D' . $row, $sumReceive + $sumPay);

        $sheet->getStyle('D' . $row)->getFont()->setColor(new Color(($sumReceive + $sumPay) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $sheet->getStyle('B2:B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('C2:C' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));

        $sheet->getStyle('A1:' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B1:' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);

        $sheet->getStyle('B2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }
}
