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

class ContractorSheet implements
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
        return 'Подрядчикам';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($this->object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR, ['with_sorted_details' => true]);

        $sheet->setCellValue('A1', 'Контрагент');
        $sheet->setCellValue('B1', 'ИНН');
        $sheet->setCellValue('C1', 'Долг СМР');
        $sheet->setCellValue('D1', 'Аванс');
        $sheet->setCellValue('E1', 'Неотработанный аванс');
        $sheet->setCellValue('F1', 'Гарантийное удержание');
        $sheet->setCellValue('G1', 'ГУ срок наступил');
        $sheet->setCellValue('H1', 'Остаток оплаты по договору');

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

//        if(! auth()->user()->hasRole('finance-object-user-mini')) {
//
//        }

        $sheet->getRowDimension(1)->setRowHeight(45);

        $row = 2;
        foreach ($contractorDebts['organizations'] as $organizationData) {
            if (! is_valid_amount_in_range($organizationData['total_amount'])) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $organizationData['organization_name']);
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, $organizationData['amount']);
            $sheet->setCellValue('D' . $row, $organizationData['avans']);
            $sheet->setCellValue('E' . $row, $organizationData['unwork_avans']);
            $sheet->setCellValue('F' . $row, $organizationData['guarantee']);
            $sheet->setCellValue('G' . $row, $organizationData['guarantee_deadline']);
            $sheet->setCellValue('H' . $row, $organizationData['balance_contract']);

            $sheet->getRowDimension($row)->setRowHeight(40);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:H' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:H1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('C2:H' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('C2:H' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
    }
}
