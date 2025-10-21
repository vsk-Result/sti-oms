<?php

namespace App\Exports\Object\PivotPayments\Sheets;

use App\Models\KostCode;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\PivotObjectDebt;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    public function __construct(
        private BObject $object,
        private array $services
    ) {}

    public function title(): string
    {
        return 'Свод по расходам';
    }

    public function styles(Worksheet $sheet): void
    {
        $object = $this->object;

        $this->fillPaymentsBlock($sheet, $object);
        $this->fillDebtsBlock($sheet, $object);
    }

    public function fillPaymentsBlock(&$sheet, $object)
    {
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(80);
        $sheet->getColumnDimension('C')->setWidth(30);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);
        $sheet->getRowDimension(5)->setRowHeight(20);
        $sheet->getRowDimension(6)->setRowHeight(20);
        $sheet->getRowDimension(7)->setRowHeight(20);
        $sheet->getRowDimension(8)->setRowHeight(20);
        $sheet->getRowDimension(9)->setRowHeight(20);


        $sheet->mergeCells('A1:C1');

        $sheet->setCellValue('A1', 'Фактические расходы по объекту ' . $object->getName());

        $sheet->setCellValue('A2', 'Категория');
        $sheet->setCellValue('B2', 'Статья затрат');
        $sheet->setCellValue('C2', 'Сумма, руб.');

        $sheet->getStyle('A2:C2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');

        $sheet->setCellValue('A3', 'Материалы фикс. часть');
        $sheet->setCellValue('A4', 'Материалы изменяемая часть');
        $sheet->setCellValue('A5', 'Общежитие изменяемая часть (Протокол № 1)');
        $sheet->setCellValue('A6', 'Подрядные работы');
        $sheet->setCellValue('A7', 'Зарплата ИТР');
        $sheet->setCellValue('A8', 'Зарплата Рабочие');
        $sheet->setCellValue('A9', 'Зарплатные налоги');
        $sheet->setCellValue('A10', 'Накладные/Услуги');

        $totalMaterialPayments = Payment::where('object_id', $object->id)
            ->where('category', Payment::CATEGORY_MATERIAL)
            ->where('amount', '<', 0)
            ->sum('amount');

        $totalMaterialFloatPayments = Payment::where('object_id', $object->id)
            ->where('category', Payment::CATEGORY_MATERIAL)
            ->where('amount', '<', 0)
            ->where('description', 'LIKE', '123')
            ->sum('amount');

        $totalRadPayments = Payment::where('object_id', $object->id)
            ->where('category', Payment::CATEGORY_RAD)
            ->where('amount', '<', 0)
            ->sum('amount');

        $itrSalary = 0;
        $ITRSalaryPivot = Cache::get('itr_salary_pivot_data_excel', []);

        foreach ($ITRSalaryPivot as $date => $pivot) {
            foreach ($pivot['objects'] as $code => $am) {
                if ($code == $object->code) {
                    $itrSalary += $am;
                }
            }
        }

        $workersSalary = 0;
//        $workersSalary = (float) WorkhourPivot::where('is_main', true)->where('code', $object->code)->sum('amount');
        $salaryTax = Payment::where('object_id', $object->id)->where('amount', '<', 0)->whereIn('code', ['7.3', '7.3.1', '7.3.2'])->sum('amount');


        $sheet->setCellValue('C3', $totalMaterialPayments - $totalMaterialFloatPayments);
        $sheet->setCellValue('C4', $totalMaterialFloatPayments);
        $sheet->setCellValue('C5', 0);
        $sheet->setCellValue('C6', $totalRadPayments);
        $sheet->setCellValue('C7', $itrSalary);
        $sheet->setCellValue('C8', $workersSalary);
        $sheet->setCellValue('C9', $salaryTax);
        $sheet->setCellValue('C10', 'Накладные/Услуги');

        $codesWithId = KostCode::getCodesWithId();
        $servicePayments = Payment::where('object_id', $object->id)
            ->where('category', Payment::CATEGORY_OPSTE)
            ->where('amount', '<', 0)
            ->get();

        $row = 10;
        foreach ($servicePayments->sort(function($a, $b) use($codesWithId) {
            return ($codesWithId[$a->code] ?? 0) - ($codesWithId[$b->code] ?? 0);
        })->groupBy('code') as $code => $payments) {
            $sheet->setCellValue('B' . $row, KostCode::getTitleByCode($code));
            $sheet->setCellValue('C' . $row, $payments->sum('amount'));

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Накладные/Услуги Итог');
        $sheet->setCellValue('C' . $row, $servicePayments->sum('amount'));

        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;

        $total = $totalMaterialPayments + $totalRadPayments + $itrSalary + $workersSalary + $salaryTax + $servicePayments->sum('amount');

        $sheet->setCellValue('A' . $row, 'Итого:');
        $sheet->setCellValue('C' . $row, $total);

        $sheet->getRowDimension($row)->setRowHeight(30);

        $sheet->getStyle('A1:C9')->getFont()->setBold(true);
        $sheet->getStyle('A1:A10')->getFont()->setBold(true);
        $sheet->getStyle('A' . ($row - 1) . ':C' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:C' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('C3:C' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A1:C2')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A3:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('C3:C' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');

        $sheet->getStyle('A' . ($row - 1) . ':C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');

        $row += 3;
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row . ':C' . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setName('Calibri')->setSize(16);
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);

        $sheet->setCellValue('A' . $row, 'Общие затраты (расходы центрального офиса, центрального склада, НДС)');
        $sheet->setCellValue('C' . $row, $this->services['pivotInfo']['general_balance']);
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $row += 3;
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setName('Calibri')->setSize(16);
        $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->setCellValue('B' . $row, 'Итого с общими затратами:');
        $sheet->setCellValue('C' . $row, $total + $this->services['pivotInfo']['general_balance']);
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');


        $row += 3;
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setName('Calibri')->setSize(16);
        $sheet->setCellValue('B' . $row, 'Итого с общими затратами и долгами на ' . now()->format('d.m.Y') . ':');
        $sheet->setCellValue('C' . $row, 0);
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');
    }

    public function fillDebtsBlock(&$sheet, $object)
    {
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(80);
        $sheet->getColumnDimension('G')->setWidth(30);

        $sheet->mergeCells('E1:G1');

        $sheet->setCellValue('E1', 'Долги по объекту ' . $object->getName());

        $sheet->setCellValue('E2', 'Категория');
        $sheet->setCellValue('F2', 'Статья затрат');
        $sheet->setCellValue('G2', 'Сумма, руб.');

        $sheet->getStyle('E2:G2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');


        $sheet->setCellValue('E3', 'Материалы фикс. часть');
        $sheet->setCellValue('E4', 'Материалы изменяемая часть');
        $sheet->setCellValue('E5', 'Подрядные работы');
        $sheet->setCellValue('E6', 'Зарплата ИТР и налоги');
        $sheet->setCellValue('E7', 'Зарплата Рабочие и налоги');
        $sheet->setCellValue('E8', 'Накладные/Услуги');

        $contractorDebts = $this->services['pivotObjectDebtService']->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR);
        $providerDebts = $this->services['pivotObjectDebtService']->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER);
        $serviceDebts = $this->services['pivotObjectDebtService']->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE);

        $totalMaterialFixPayments = $providerDebts['total']['amount_fix'];
        $totalMaterialFloatPayments = $providerDebts['total']['amount_float'];
        $totalContractorPayments = $contractorDebts['total']['total_amount'];
        $totalServicePayments = $serviceDebts['total']['total_amount'];
        $itrSalary = $object->getITRSalaryDebt();
        $workSalaryDebt = $object->getWorkSalaryDebt();

        $sheet->setCellValue('G3', $totalMaterialFixPayments);
        $sheet->setCellValue('G4', $totalMaterialFloatPayments);
        $sheet->setCellValue('G5', $totalContractorPayments);
        $sheet->setCellValue('G6', $itrSalary);
        $sheet->setCellValue('G7', $workSalaryDebt);

        $groupedByCode = [];
        foreach ($serviceDebts['organizations'] as $organizationInfo) {
            foreach ($organizationInfo['details'] as $detail) {
                $code = $detail['code'];

                if (! isset($groupedByCode[$code])) {
                    $groupedByCode[$code] = 0;
                }

                $groupedByCode[$code] += $detail['amount'];
            }
        }

        ksort($groupedByCode);

        $row = 8;
        foreach ($groupedByCode as $code => $amount) {
            $sheet->setCellValue('F' . $row, KostCode::getTitleByCode($code));
            $sheet->setCellValue('G' . $row, $amount);

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $sheet->setCellValue('E' . $row, 'Накладные/Услуги Итог');
        $sheet->setCellValue('G' . $row, $totalServicePayments);

        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;

        $sheet->setCellValue('E' . $row, 'Итого:');
        $sheet->setCellValue('G' . $row, $totalMaterialFixPayments + $totalMaterialFloatPayments + $totalContractorPayments + $itrSalary + $workSalaryDebt + $totalServicePayments);

        $sheet->getRowDimension($row)->setRowHeight(30);

        $sheet->getStyle('E1:E8')->getFont()->setBold(true);
        $sheet->getStyle('G1:G7')->getFont()->setBold(true);
        $sheet->getStyle('E' . ($row - 1) . ':G' . $row)->getFont()->setBold(true);

        $sheet->getStyle('E1:G' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('G3:G' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('E1:G2')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('E3:F' . $row)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('G3:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');

        $sheet->getStyle('E' . ($row - 1) . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
    }
}
