<?php

namespace App\Exports\Finance\GeneralCosts\Sheets;

use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    WithEvents
{
    private string $sheetName;

    public function __construct(string $sheetName)
    {
        $this->sheetName = $sheetName;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objects = BObject::whereIn('code', $codes)->orderByDesc('code')->with(['customers', 'payments' => function($q) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('amount', '>=', 0);
        }])->get();

        $object27_1 = \App\Models\Object\BObject::where('code', '27.1')->first();
        $object27_8 = \App\Models\Object\BObject::where('code', '27.8')->first();

        $periods = [
            [
                'start_date' => '2017-01-01',
                'end_date' => '2017-12-31',
                'bonus' => 0,
            ],
            [
                'start_date' => '2018-01-01',
                'end_date' => '2018-12-31',
                'bonus' => 21421114,
            ],
            [
                'start_date' => '2019-01-01',
                'end_date' => '2019-12-31',
                'bonus' => (39760000 + 692048),
            ],
            [
                'start_date' => '2020-01-01',
                'end_date' => '2020-12-31',
                'bonus' => (2000000 + 418000 + 1615000),
            ],
            [
                'start_date' => '2021-01-01',
                'end_date' => '2021-03-02',
                'bonus' => 600000,
            ],
            [
                'start_date' => '2021-03-03',
                'end_date' => '2021-12-31',
                'bonus' => (600000 + 68689966),
            ],
            [
                'start_date' => '2022-01-01',
                'end_date' => '2022-10-11',
                'bonus' => 0,
            ],
            [
                'start_date' => '2022-10-12',
                'end_date' => '2022-12-31',
                'bonus' => 0,
            ],
            [
                'start_date' => '2023-01-01',
                'end_date' => '2023-07-20',
                'bonus' => 0,
            ],
            [
                'start_date' => '2023-07-21',
                'end_date' => '2023-11-28',
                'bonus' => 0,
            ],
            [
                'start_date' => '2023-11-29',
                'end_date' => '2023-12-31',
                'bonus' => 0,
            ],
            [
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
                'bonus' => 0,
            ],
        ];

        $periods = array_reverse($periods);

        $generalTotalAmount = 0;
        $generalInfo = [];
        foreach ($periods as $index => $period) {
            $datesBetween = [$period['start_date'], $period['end_date']];
            $paymentQuery = \App\Models\Payment::query()->whereBetween('date', $datesBetween)->where('company_id', 1);
            $generalAmount = (clone $paymentQuery)->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount')
                + (clone $paymentQuery)->where('object_id', $object27_1->id)->sum('amount')
                + ((clone $paymentQuery)->where('object_id', $object27_8->id)->sum('amount') * 0.7)
                + $period['bonus'];

            $generalInfo[$index] = [
                'start_date' => $period['start_date'],
                'end_date' => $period['end_date'],
                'general_amount' => $generalAmount,
                'info' => \App\Services\ObjectService::getGeneralCostsByPeriod($period['start_date'], $period['end_date'], $period['bonus']),
            ];

            $generalTotalAmount += $generalAmount;
        }

        $averagePercents = [];
        foreach($objects as $object) {
            $percentSum = 0;
            $percentCount = 0;

            foreach($generalInfo as $info) {
                if (isset($info['info'][$object->id])) {
                    $percent = ($info['info'][$object->id]['cuming_amount'] > 0 ? abs($info['info'][$object->id]['general_amount'] / $info['info'][$object->id]['cuming_amount']) : 0);
                    $percentSum += $percent;
                    $percentCount++;
                }
            }

            $averagePercents[$object->id] = $percentCount > 0 ? $percentSum / $percentCount : 0;
        }

        $columns = ['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP'];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'РАЗБИВКА ОБЩИХ ЗАТРАТ ПО ГОДАМ');
        $sheet->setCellValue('B1', 'ИТОГО');
        $sheet->setCellValue('D1', $generalTotalAmount);

        $columnIndex = 0;
        foreach($generalInfo as $info) {
            $sheet->setCellValue($columns[$columnIndex] . '1', 'С ' . Carbon::parse($info['start_date'])->format('d.m.Y') . ' ПО ' .  Carbon::parse($info['end_date'])->format('d.m.Y'));
            $sheet->setCellValue($columns[$columnIndex + 2] . '1', $info['general_amount']);
            $columnIndex += 3;
        }

        $sheet->getStyle('D1')->getFont()->setColor(new Color($generalTotalAmount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $columnIndex = 2;
        foreach($generalInfo as $info) {
            $sheet->getStyle($columns[$columnIndex] . '1')->getFont()->setColor(new Color($info['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $columnIndex += 3;
        }

        $sheet->setCellValue('A2', 'Объект');
        $sheet->setCellValue('B2', 'Получено');
        $sheet->setCellValue('C2', '%');
        $sheet->setCellValue('D2', 'Общие расходы');

        $columnIndex = 0;
        foreach($generalInfo as $info) {
            $sheet->setCellValue($columns[$columnIndex] . '2', 'Получено');
            $sheet->setCellValue($columns[$columnIndex + 1] . '2', '%');
            $sheet->setCellValue($columns[$columnIndex + 2] . '2', 'Общие расходы на объект');
            $columnIndex += 3;
        }

        $row = 3;
        foreach($objects as $object) {
            $sheet->setCellValue('A' . $row, $object->getName());

            $totalCuming = 0;
            $totalGeneral = 0;
            foreach($generalInfo as $info) {
                $totalCuming += ($info['info'][$object->id]['cuming_amount'] ?? 0);
                $totalGeneral += ($info['info'][$object->id]['general_amount'] ?? 0);
            }

            $sheet->setCellValue('B' . $row,$totalCuming);
            $sheet->setCellValue('C' . $row, $totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0);
            $sheet->setCellValue('D' . $row, $totalGeneral);

            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCuming < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalGeneral < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex = 0;
            foreach($generalInfo as $info) {
                if (isset ($info['info'][$object->id])) {
                    $sheet->setCellValue($columns[$columnIndex] . $row, $info['info'][$object->id]['cuming_amount']);
                    $sheet->setCellValue($columns[$columnIndex + 1] . $row, $info['info'][$object->id]['cuming_amount'] > 0 ? abs($info['info'][$object->id]['general_amount'] / $info['info'][$object->id]['cuming_amount']) : 0);
                    $sheet->setCellValue($columns[$columnIndex + 2] . $row, $info['info'][$object->id]['general_amount']);

                    $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($info['info'][$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle($columns[$columnIndex + 2] . $row)->getFont()->setColor(new Color($info['info'][$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                $columnIndex += 3;
            }

            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;
        }

        $row--;

        $sheet->getRowDimension(1)->setRowHeight(50);

        $sheet->getColumnDimension('A')->setWidth(43);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        foreach($columns as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }

        $sheet->getStyle('A1:AN' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(true);

        $sheet->getStyle('A1:A' . $row)->getAlignment()->setHorizontal('left');
        $sheet->getStyle('B1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('E1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('H1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('K1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('N1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('Q1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('T1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('W1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('Z1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('AC1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('AF1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('AI1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('AL1')->getAlignment()->setHorizontal('left');

        $sheet->getStyle('F1:F' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('I1:I' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('L1:L' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('O1:O' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('R1:R' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('U1:U' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('T1:T' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('X1:X' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('AA1:AA' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('AD1:AD' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('AG1:AG' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('AJ1:AJ' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('AM1:AM' . $row)->getAlignment()->setHorizontal('center');

        $sheet->getStyle('B2:AN2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C3:C' . $row)->getAlignment()->setHorizontal('center');

        $sheet->getStyle('A1:AN1')->getFont()->setBold(true);
        $sheet->getStyle('B1:D' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:AN2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('B1:D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:AN' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'dddddd']]]
        ]);

        $sheet->getStyle('B1:D' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('E1:G' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('H1:J' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('K1:M' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('N1:P' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('Q1:S' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('T1:V' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('W1:Y' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('Z1:AB' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('AC1:AE' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('AF1:AH' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('AI1:AK' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('AL1:AN' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);


        $sheet->getStyle('B1:AN' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C3:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('F3:F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('I3:I' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('L3:L' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('O3:O' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('R3:R' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('U3:U' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('X3:X' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AA3:AA' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AD3:AD' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AG3:AG' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AJ3:AJ' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AM3:AM' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->freezePane('B2');
            },
        ];
    }
}
