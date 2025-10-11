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

class ServiceSheet implements
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
        return 'За услуги';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($this->object->id, PivotObjectDebt::DEBT_TYPE_SERVICE, ['with_sorted_details' => true]);

        $sheet->setCellValue('A1', 'Контрагент');
        $sheet->setCellValue('B1', 'ИНН');
        $sheet->setCellValue('C1', 'Аванс к оплате');
        $sheet->setCellValue('D1', 'Долг за оказанные услуги');

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

//        if(! auth()->user()->hasRole('finance-object-user-mini')) {
//
//        }

        $sheet->getRowDimension(1)->setRowHeight(45);

        $row = 2;
        foreach ($serviceDebts['organizations'] as $organizationData) {
            $sheet->setCellValue('A' . $row, $organizationData['organization_name']);
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, $organizationData['avans']);
            $sheet->setCellValue('D' . $row, $organizationData['amount']);

            $sheet->getRowDimension($row)->setRowHeight(40);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:D' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('C2:D' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('C2:D' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
    }
}
