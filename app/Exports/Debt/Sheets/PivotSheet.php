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

class PivotSheet implements
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
        return 'Сводная';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($this->object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR, ['with_sorted_details' => true]);
        $providerDebts = $this->pivotObjectDebtService->getPivotDebts($this->object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER, ['with_sorted_details' => true]);
        $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($this->object->id, PivotObjectDebt::DEBT_TYPE_SERVICE, ['with_sorted_details' => true]);

        $sheet->setCellValue('A1', 'Долг подрядчикам');
        $sheet->setCellValue('A3', 'Контрагент');
        $sheet->setCellValue('B3', 'Сумма');

        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getStyle('A1:B3')->getFont()->setBold(true);

        $sheet->setCellValue('B1', $contractorDebts['total']['total_amount']);


        if(! auth()->user()->hasRole('finance-object-user-mini')) {
            $sheet->setCellValue('E1', 'Долг поставщикам');
            $sheet->setCellValue('E3', 'Контрагент');
            $sheet->setCellValue('F3', 'Сумма');

            $sheet->setCellValue('I1', 'Долг за услуги');
            $sheet->setCellValue('I3', 'Контрагент');
            $sheet->setCellValue('J3', 'Сумма');

            $sheet->getColumnDimension('E')->setWidth(40);
            $sheet->getColumnDimension('I')->setWidth(40);

            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('J')->setWidth(20);

            $sheet->getStyle('E1:F3')->getFont()->setBold(true);
            $sheet->getStyle('I1:J3')->getFont()->setBold(true);

            $sheet->setCellValue('F1', $providerDebts['total']['amount']);
            $sheet->setCellValue('J1', $serviceDebts['total']['amount']);
        }

        $sheet->getRowDimension(1)->setRowHeight(35);
        $sheet->getRowDimension(3)->setRowHeight(40);


        // Подрядчики
        $row = 4;
        foreach ($contractorDebts['organizations'] as $organizationData) {
            if (! is_valid_amount_in_range($organizationData['total_amount'])) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $organizationData['organization_name']);
            $sheet->setCellValue('B' . $row, $organizationData['total_amount']);

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

        if(! auth()->user()->hasRole('finance-object-user-mini')) {

            // Поставщики
            $row = 4;
            foreach ($providerDebts['organizations'] as $organizationData) {
                if (!is_valid_amount_in_range($organizationData['amount'])) {
                    continue;
                }

                $sheet->setCellValue('E' . $row, $organizationData['organization_name']);
                $sheet->setCellValue('F' . $row, $organizationData['amount']);

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
            foreach ($serviceDebts['organizations'] as $organizationData) {
                if (!is_valid_amount_in_range($organizationData['amount'])) {
                    continue;
                }

                $sheet->setCellValue('I' . $row, $organizationData['organization_name']);
                $sheet->setCellValue('J' . $row, $organizationData['amount']);

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
}
