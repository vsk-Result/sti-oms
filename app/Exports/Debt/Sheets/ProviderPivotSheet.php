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

class ProviderPivotSheet implements
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
        return 'Сводная по поставщикам';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Контрагент');
        $sheet->setCellValue('C1', 'ИНН');
        $sheet->setCellValue('D1', 'Фиксированная часть');
        $sheet->setCellValue('E1', 'Изменяемая часть');
        $sheet->setCellValue('F1', 'Сумма долга');

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

//        if(! auth()->user()->hasRole('finance-object-user-mini')) {
//
//        }

        $sheet->getRowDimension(1)->setRowHeight(45);

        $closedObjects = BObject::where('status_id', Status::STATUS_BLOCKED)->orderBy('code')->get();
        $row = 2;

        foreach ($closedObjects as $object) {
            $providerDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER, ['with_sorted_details' => true]);

            $totalRow = $row;
            $total = [
                'amount_fix' => 0,
                'amount_float' => 0,
                'total_amount' => 0,
            ];

            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;

            foreach ($providerDebts['organizations'] as $organizationData) {
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, $organizationData['organization_name']);
                $sheet->setCellValue('C' . $row, '');
                $sheet->setCellValue('D' . $row, $organizationData['amount_fix']);
                $sheet->setCellValue('E' . $row, $organizationData['amount_float']);
                $sheet->setCellValue('F' . $row, $organizationData['total_amount']);

                $total['amount_fix'] += $organizationData['amount_fix'];
                $total['amount_float'] += $organizationData['amount_float'];
                $total['total_amount'] += $organizationData['total_amount'];

                $sheet->getRowDimension($row)->setRowHeight(40);
                $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setItalic(true);

                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                $row++;
            }

            $sheet->setCellValue('A' . $totalRow, $object->getName());
            $sheet->setCellValue('B' . $totalRow, '');
            $sheet->setCellValue('C' . $totalRow, '');
            $sheet->setCellValue('D' . $totalRow, $total['amount_fix']);
            $sheet->setCellValue('E' . $totalRow, $total['amount_float']);
            $sheet->setCellValue('F' . $totalRow, $total['total_amount']);

            $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        }

        $row--;

        $sheet->getStyle('A1:F' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:F1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:C' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('D2:F' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('D2:F' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
    }
}
