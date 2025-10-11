<?php

namespace App\Exports\Debt\Sheets;

use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use App\Models\Status;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicePivotSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function title(): string
    {
        return 'Сводная по услугам';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Контрагент');
        $sheet->setCellValue('C1', 'ИНН');
        $sheet->setCellValue('D1', 'Сумма долга');

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);

        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

//        if(! auth()->user()->hasRole('finance-object-user-mini')) {
//
//        }

        $sheet->getRowDimension(1)->setRowHeight(45);

        $closedObjects = BObject::where('status_id', Status::STATUS_BLOCKED)->orderBy('code')->get();
        $row = 2;

        foreach ($closedObjects as $object) {
            $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE, ['with_sorted_details' => true]);

            if (count($serviceDebts['organizations']) === 0) {
                continue;
            }

            $totalRow = $row;
            $total = [
                'amount' => 0,
            ];

            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;

            foreach ($serviceDebts['organizations'] as $organizationData) {
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, $organizationData['organization_name']);
                $sheet->setCellValue('C' . $row, '');
                $sheet->setCellValue('D' . $row, $organizationData['total_amount']);

                $total['amount'] += $organizationData['total_amount'];

                $sheet->getRowDimension($row)->setRowHeight(40);
                $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setItalic(true);

                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                $row++;
            }

            $sheet->setCellValue('A' . $totalRow, $object->getName());
            $sheet->setCellValue('B' . $totalRow, '');
            $sheet->setCellValue('C' . $totalRow, '');
            $sheet->setCellValue('D' . $totalRow, $total['amount']);

            $sheet->getStyle('A' . $totalRow . ':D' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $totalRow . ':D' . $totalRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        }

        $row--;

        $sheet->getStyle('A1:D' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:D1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:C' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('D2:D' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
    }
}
