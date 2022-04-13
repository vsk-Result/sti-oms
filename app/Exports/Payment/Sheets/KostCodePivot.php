<?php

namespace App\Exports\Payment\Sheets;

use App\Models\KostCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KostCodePivot implements
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

        $sheet->setCellValue('A1', 'Статья');
        $sheet->setCellValue('B1', 'Приходы');
        $sheet->setCellValue('C1', 'Расходы');
        $sheet->setCellValue('D1', 'Общая сумма');

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        $row = 2;
        $codes = KostCode::getCodes();
        $groupedByCodePayments = (clone $this->payments)->get()->sortBy('code')->groupBy('code');

        foreach ($groupedByCodePayments as $code => $payments) {
            $total = $payments->sum('amount');

            $sheet->setCellValue('A' . $row, $codes[$code] ?? $code);
            $sheet->setCellValue('B' . $row, $payments->where('amount', '>=', 0)->sum('amount'));
            $sheet->setCellValue('C' . $row, $payments->where('amount', '<', 0)->sum('amount'));
            $sheet->setCellValue('D' . $row, $total);

            $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($total < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $sheet->getRowDimension($row)->setRowHeight(30);
            $row++;
        }

        $row--;

        $sheet->getStyle('B2:B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('C2:C' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));

        $sheet->getStyle('A1:D' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A1:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B1:D' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);

        $sheet->getStyle('B2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    }
}
