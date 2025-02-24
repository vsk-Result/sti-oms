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

class ContractorPivotSheet implements
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
        return 'Сводная по подрядчикам';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Контрагент');
        $sheet->setCellValue('C1', 'ИНН');
        $sheet->setCellValue('D1', 'Долг СМР');
        $sheet->setCellValue('E1', 'Аванс');
        $sheet->setCellValue('F1', 'Неотработанный аванс');
        $sheet->setCellValue('G1', 'Гарантийное удержание');
        $sheet->setCellValue('H1', 'ГУ срок наступил');
        $sheet->setCellValue('I1', 'Остаток оплаты по договору');

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(18);

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

//        if(! auth()->user()->hasRole('finance-object-user-mini')) {
//
//        }

        $sheet->getRowDimension(1)->setRowHeight(45);

        $closedObjects = BObject::where('status_id', Status::STATUS_BLOCKED)->orderBy('code')->get();
        $row = 2;

        foreach ($closedObjects as $object) {
            $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR, ['with_sorted_details' => true]);

            if (count($contractorDebts['organizations']) === 0) {
                continue;
            }

            $totalRow = $row;
            $total = [
                'amount' => 0,
                'avans' => 0,
                'unwork_avans' => 0,
                'guarantee' => 0,
                'guarantee_deadline' => 0,
                'balance_contract' => 0,
            ];

            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;

            foreach ($contractorDebts['organizations'] as $organizationData) {
                $sheet->setCellValue('A' . $row, '');
                $sheet->setCellValue('B' . $row, $organizationData['organization_name']);
                $sheet->setCellValue('C' . $row, '');
                $sheet->setCellValue('D' . $row, $organizationData['amount']);
                $sheet->setCellValue('E' . $row, $organizationData['avans']);
                $sheet->setCellValue('F' . $row, $organizationData['unwork_avans']);
                $sheet->setCellValue('G' . $row, $organizationData['guarantee']);
                $sheet->setCellValue('H' . $row, $organizationData['guarantee_deadline']);
                $sheet->setCellValue('I' . $row, $organizationData['balance_contract']);

                $total['amount'] += $organizationData['amount'];
                $total['avans'] += $organizationData['avans'];
                $total['unwork_avans'] += $organizationData['unwork_avans'];
                $total['guarantee'] += $organizationData['guarantee'];
                $total['guarantee_deadline'] += $organizationData['guarantee_deadline'];
                $total['balance_contract'] += $organizationData['balance_contract'];

                $sheet->getRowDimension($row)->setRowHeight(40);
                $sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setItalic(true);

                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                $row++;
            }

            $sheet->setCellValue('A' . $totalRow, $object->getName());
            $sheet->setCellValue('B' . $totalRow, '');
            $sheet->setCellValue('C' . $totalRow, '');
            $sheet->setCellValue('D' . $totalRow, $total['amount']);
            $sheet->setCellValue('E' . $totalRow, $total['avans']);
            $sheet->setCellValue('F' . $totalRow, $total['unwork_avans']);
            $sheet->setCellValue('G' . $totalRow, $total['guarantee']);
            $sheet->setCellValue('H' . $totalRow, $total['guarantee_deadline']);
            $sheet->setCellValue('I' . $totalRow, $total['balance_contract']);

            $sheet->getStyle('A' . $totalRow . ':I' . $totalRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $totalRow . ':I' . $totalRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        }

        $row--;

        $sheet->getStyle('A1:I' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:I1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:C' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('D2:I' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('D2:I' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
    }
}
