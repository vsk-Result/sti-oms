<?php

namespace App\Exports\Pivot\Act\Sheets;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotClosedSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    public function __construct(string $sheetName)
    {
        $this->sheetName = $sheetName;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $object = BObject::where('code', '000')->first();
        $guarantees = $object->guarantees;
        $guAmount = $guarantees->sum('amount') - $object->guaranteePayments()->sum('amount');

        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Гарантийное удержание');

        $sheet->setCellValue('A2', 'Итого');
        $sheet->setCellValue('B2', $guAmount);
        $sheet->getStyle('B2')->getFont()->setColor(new Color($guAmount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $rowIndex = 3;
        foreach ($guarantees as $guarantee) {
            $sheet->setCellValue('A' . $rowIndex, mb_substr($guarantee->state, mb_strpos($guarantee->state, 'Объект ') + 7, 3));
            $sheet->setCellValue('B' . $rowIndex, $guarantee->amount - $guarantee->payments->sum('amount'));
            $sheet->getRowDimension($rowIndex)->setRowHeight(20);
            $rowIndex++;
        }

        $rowIndex--;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);

        $sheet->getStyle('A1:B2')->getFont()->setBold(true);
        $sheet->getStyle('B1:B1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:B' . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);
        $sheet->getStyle('B2:B'. $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:B'. $rowIndex)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('A2:B2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');

        $sheet->setAutoFilter('A1:B' . $rowIndex);
    }
}
