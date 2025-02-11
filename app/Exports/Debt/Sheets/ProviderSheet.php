<?php

namespace App\Exports\Debt\Sheets;

use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProviderSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private BObject $object;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(BObject $object, PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function title(): string
    {
        return 'Поставщикам';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $providerDebts = $this->pivotObjectDebtService->getPivotDebts($this->object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER, ['with_sorted_details' => true]);

        $sheet->setCellValue('A1', 'Контрагент');
        $sheet->setCellValue('B1', 'ИНН');
        $sheet->setCellValue('C1', 'Фиксированная часть');
        $sheet->setCellValue('D1', 'Изменяемая часть');
        $sheet->setCellValue('E1', 'Сумма долга');

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

//        if(! auth()->user()->hasRole('finance-object-user-mini')) {
//
//        }

        $sheet->getRowDimension(1)->setRowHeight(45);

        $row = 2;
        foreach ($providerDebts['organizations'] as $organizationData) {
            if (! is_valid_amount_in_range($organizationData['total_amount'])) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $organizationData['organization_name']);
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, $organizationData['amount_fix']);
            $sheet->setCellValue('D' . $row, $organizationData['amount_float']);
            $sheet->setCellValue('E' . $row, $organizationData['total_amount']);

            $sheet->getRowDimension($row)->setRowHeight(40);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:E' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:E1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('C2:E' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('C2:E' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
    }
}
