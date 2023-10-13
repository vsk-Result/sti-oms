<?php

namespace App\Exports\Debt\Sheets;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private BObject $object;

    public function __construct(BObject $object)
    {
        $this->object = $object;
    }

    public function title(): string
    {
        return 'Сводная';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Долг подрядчикам');
        $sheet->setCellValue('A3', 'Контрагент');
        $sheet->setCellValue('B3', 'Сумма');

        $sheet->setCellValue('E1', 'Долг поставщикам');
        $sheet->setCellValue('E3', 'Контрагент');
        $sheet->setCellValue('F3', 'Сумма');

        $sheet->setCellValue('I1', 'Долг за услуги');
        $sheet->setCellValue('I3', 'Контрагент');
        $sheet->setCellValue('J3', 'Сумма');

        $sheet->getRowDimension(1)->setRowHeight(35);
        $sheet->getRowDimension(3)->setRowHeight(40);

        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('I')->setWidth(40);

        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);

        $sheet->getStyle('A1:B3')->getFont()->setBold(true);
        $sheet->getStyle('E1:F3')->getFont()->setBold(true);
        $sheet->getStyle('I1:J3')->getFont()->setBold(true);

        $sheet->setCellValue('B1', $this->object->getContractorDebtsAmount());
        $sheet->setCellValue('F1', $this->object->getProviderDebtsAmount());
        $sheet->setCellValue('J1', $this->object->getServiceDebtsAmount());

        // Подрядчики
        $row = 4;
        foreach ($this->object->getContractorDebts() as $organization => $amount) {
            $sheet->setCellValue('A' . $row, substr($organization, strpos($organization, '::') + 2));
            $sheet->setCellValue('B' . $row, $amount);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:B' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B1:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('B1:B1')->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('B4:B' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('B1:B' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');

        // Поставщики
        $row = 4;
        foreach ($this->object->getProviderDebts() as $organization => $amount) {
            $sheet->setCellValue('E' . $row, substr($organization, strpos($organization, '::') + 2));
            $sheet->setCellValue('F' . $row, $amount);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('E1:F' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('E1:E' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('F1:F' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('F1:F1')->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('F4:F' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('F1:F' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');

        // Услуги
        $row = 4;
        foreach ($this->object->getServiceDebts() as $organization => $amount) {
            $sheet->setCellValue('I' . $row, substr($organization, strpos($organization, '::') + 2));
            $sheet->setCellValue('J' . $row, $amount);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('I1:J' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('I1:I' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('J1:J' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('J1:J1')->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('J4:J' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('J1:J' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
    }
}
