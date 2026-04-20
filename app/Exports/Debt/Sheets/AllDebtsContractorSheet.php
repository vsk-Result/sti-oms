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

class AllDebtsContractorSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private array $object_ids;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(array $object_ids, PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->object_ids = $object_ids;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function title(): string
    {
        return 'Подрядчикам';
    }

    public function styles(Worksheet $sheet): void
    {
        $final_array = [];
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        foreach ($this->object_ids as $object_id) {
            $object_name = BObject::query()->find($object_id)->getName();
            $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object_id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR, ['with_sorted_details' => true]);
            foreach ($contractorDebts['organizations'] as $organization) {
                $final_array[] = array_merge($organization, ['object_name' => $object_name]);
            }
        }
        usort($final_array, fn($a, $b) => $a['organization_name'] <=> $b['organization_name']);

        $sheet->setCellValue('A1', 'Контрагент');
        $sheet->setCellValue('B1', 'Объект');
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
        foreach ($final_array as $record) {
            $sheet->setCellValue('A' . $row, $record['organization_name']);
            $sheet->setCellValue('B' . $row, $record['object_name']);
            $sheet->setCellValue('C' . $row, $record['amount']);
            $sheet->setCellValue('D' . $row, $record['avans']);
            $sheet->setCellValue('E' . $row, $record['unwork_avans']);
            $sheet->setCellValue('F' . $row, $record['guarantee']);
            $sheet->setCellValue('G' . $row, $record['guarantee_deadline']);
            $sheet->setCellValue('H' . $row, $record['balance_contract']);

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
