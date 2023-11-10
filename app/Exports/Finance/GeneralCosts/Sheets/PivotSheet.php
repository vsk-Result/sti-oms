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
                'end_date' => '2023-12-31',
                'bonus' => 0,
            ]
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

        $columns = ['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X'];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'РАЗБИВКА ОБЩИХ ЗАТРАТ ПО ГОДАМ');
        $sheet->setCellValue('B1', 'ИТОГО');
        $sheet->setCellValue('D1', CurrencyExchangeRate::format($generalTotalAmount, 'RUB', 0, true));

        $columnIndex = 0;
        foreach($generalInfo as $info) {
            $sheet->setCellValue($columns[$columnIndex] . '1', 'С ' . Carbon::parse($info['start_date'])->format('d.m.Y') . ' ПО ' .  Carbon::parse($info['end_date'])->format('d.m.Y'));
            $sheet->setCellValue($columns[$columnIndex + 1] . '1', CurrencyExchangeRate::format($info['general_amount'], 'RUB', 0, true));
            $columnIndex += 2;
        }

        $sheet->getStyle('D1')->getFont()->setColor(new Color($generalTotalAmount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $columnIndex = 1;
        foreach($generalInfo as $info) {
            $sheet->getStyle($columns[$columnIndex] . '1')->getFont()->setColor(new Color($info['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $columnIndex += 2;
        }

        $sheet->setCellValue('A2', 'Объект');
        $sheet->setCellValue('B2', 'Получено');
        $sheet->setCellValue('C2', '%');
        $sheet->setCellValue('D2', 'Общие расходы');
        $sheet->setCellValue('E2', 'Получено');
        $sheet->setCellValue('F2', 'Общие расходы на объект');
        $sheet->setCellValue('G2', 'Получено');
        $sheet->setCellValue('H2', 'Общие расходы на объект');
        $sheet->setCellValue('I2', 'Получено');
        $sheet->setCellValue('J2', 'Общие расходы на объект');
        $sheet->setCellValue('K2', 'Получено');
        $sheet->setCellValue('L2', 'Общие расходы на объект');
        $sheet->setCellValue('M2', 'Получено');
        $sheet->setCellValue('N2', 'Общие расходы на объект');
        $sheet->setCellValue('O2', 'Получено');
        $sheet->setCellValue('P2', 'Общие расходы на объект');
        $sheet->setCellValue('Q2', 'Получено');
        $sheet->setCellValue('R2', 'Общие расходы на объект');
        $sheet->setCellValue('S2', 'Получено');
        $sheet->setCellValue('T2', 'Общие расходы на объект');
        $sheet->setCellValue('U2', 'Получено');
        $sheet->setCellValue('V2', 'Общие расходы на объект');
        $sheet->setCellValue('W2', 'Получено');
        $sheet->setCellValue('X2', 'Общие расходы на объект');

        $row = 3;
        foreach($objects as $object) {
            if ($object->code == 288) {
                $sheet->setCellValue('A' . $row, $object->getName() . ' | 1 (Строительство)');

                $totalCuming = 0;
                $totalGeneral = 0;
                foreach($generalInfo as $info) {
                    $totalCuming += ($info['info'][$object->id.'|1']['cuming_amount'] ?? 0);
                    $totalGeneral += ($info['info'][$object->id.'|1']['general_amount'] ?? 0);
                }

                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true));
                $sheet->setCellValue('C' . $row, number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2));
                $sheet->setCellValue('D' . $row, CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true));

                $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCuming < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalGeneral < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $columnIndex = 0;
                foreach($generalInfo as $info) {
                    if (isset ($info['info'][$object->id.'|1'])) {
                        $sheet->setCellValue($columns[$columnIndex] . $row, CurrencyExchangeRate::format($info['info'][$object->id.'|1']['cuming_amount'], 'RUB', 0, true));
                        $sheet->setCellValue($columns[$columnIndex + 1] . $row, CurrencyExchangeRate::format($info['info'][$object->id.'|1']['general_amount'], 'RUB', 0, true));

                        $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($info['info'][$object->id.'|1']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                        $sheet->getStyle($columns[$columnIndex + 1] . $row)->getFont()->setColor(new Color($info['info'][$object->id.'|1']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    }

                    $columnIndex += 2;
                }

                $sheet->getRowDimension($row)->setRowHeight(50);
                $row++;

                $sheet->setCellValue('A' . $row, $object->getName() . ' | 2+4 (Инженерия)');

                $totalCuming = 0;
                $totalGeneral = 0;
                foreach($generalInfo as $info) {
                    $totalCuming += ($info['info'][$object->id.'|24']['cuming_amount'] ?? 0);
                    $totalGeneral += ($info['info'][$object->id.'|24']['general_amount'] ?? 0);
                }

                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true));
                $sheet->setCellValue('C' . $row, number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2));
                $sheet->setCellValue('D' . $row, CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true));

                $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCuming < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalGeneral < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $columnIndex = 0;
                foreach($generalInfo as $info) {
                    if (isset ($info['info'][$object->id.'|24'])) {
                        $sheet->setCellValue($columns[$columnIndex] . $row, CurrencyExchangeRate::format($info['info'][$object->id.'|24']['cuming_amount'], 'RUB', 0, true));
                        $sheet->setCellValue($columns[$columnIndex + 1] . $row, CurrencyExchangeRate::format($info['info'][$object->id.'|24']['general_amount'], 'RUB', 0, true));

                        $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($info['info'][$object->id.'|24']['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                        $sheet->getStyle($columns[$columnIndex + 1] . $row)->getFont()->setColor(new Color($info['info'][$object->id.'|24']['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    }

                    $columnIndex += 2;
                }

                $sheet->getRowDimension($row)->setRowHeight(50);
                $row++;

                continue;
            }

            $sheet->setCellValue('A' . $row, $object->getName());

            $totalCuming = 0;
            $totalGeneral = 0;
            foreach($generalInfo as $info) {
                $totalCuming += ($info['info'][$object->id]['cuming_amount'] ?? 0);
                $totalGeneral += ($info['info'][$object->id]['general_amount'] ?? 0);
            }

            $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true));
            $sheet->setCellValue('C' . $row, number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2));
            $sheet->setCellValue('D' . $row, CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true));

            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCuming < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalGeneral < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex = 0;
            foreach($generalInfo as $info) {
                if (isset ($info['info'][$object->id])) {
                    $sheet->setCellValue($columns[$columnIndex] . $row, CurrencyExchangeRate::format($info['info'][$object->id]['cuming_amount'], 'RUB', 0, true));
                    $sheet->setCellValue($columns[$columnIndex + 1] . $row, CurrencyExchangeRate::format($info['info'][$object->id]['general_amount'], 'RUB', 0, true));

                    $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($info['info'][$object->id]['cuming_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle($columns[$columnIndex + 1] . $row)->getFont()->setColor(new Color($info['info'][$object->id]['general_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                $columnIndex += 2;
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
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(20);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(20);
        $sheet->getColumnDimension('S')->setWidth(20);
        $sheet->getColumnDimension('T')->setWidth(20);
        $sheet->getColumnDimension('U')->setWidth(20);
        $sheet->getColumnDimension('V')->setWidth(20);
        $sheet->getColumnDimension('W')->setWidth(20);
        $sheet->getColumnDimension('X')->setWidth(20);

        $sheet->getStyle('A1:X' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(true);

        $sheet->getStyle('A1:A' . $row)->getAlignment()->setHorizontal('left');
        $sheet->getStyle('B1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('E1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('G1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('I1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('K1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('M1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('O1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('Q1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('S1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('U1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('W1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('B2:X2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C3:C' . $row)->getAlignment()->setHorizontal('center');

        $sheet->getStyle('A1:V1')->getFont()->setBold(true);
        $sheet->getStyle('B1:D' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:X2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('B1:D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:X' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'dddddd']]]
        ]);

        $sheet->getStyle('B1:D' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('E1:F' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('G1:H' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('I1:J' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('K1:L' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('M1:N' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('O1:P' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('Q1:R' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('S1:T' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('U1:V' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('W1:X' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);
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
