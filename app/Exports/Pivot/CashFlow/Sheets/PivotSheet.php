<?php

namespace App\Exports\Pivot\CashFlow\Sheets;

use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Models\TaxPlanItem;
use App\Services\ReceivePlanService;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function title(): string
    {
        return 'Отчет';
    }

    public function styles(Worksheet $sheet): void
    {
        $periods = $this->receivePlanService->getPeriods();

        $lastColumnIndex = 2 + count($periods);
        $lastColumn = $this->getColumnWord($lastColumnIndex);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(25);
        $sheet->getRowDimension(4)->setRowHeight(25);

        $sheet->getColumnDimension('A')->setWidth(60);
        $sheet->getColumnDimension($lastColumn)->setWidth(30);

        $sheet->getStyle('A1:' . $lastColumn . '2')->getFont()->setBold(true);
        $sheet->getStyle('A3:A4')->getFont()->setBold(true);

        $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->setCellValue('A2', 'ПОСТУПЛЕНИЯ ИТОГО, в том числе:');
        $sheet->setCellValue('A3', '         Целевые авансы');
        $sheet->setCellValue('A4', '         Прочие поступления');

        $sheet->getStyle('A2:' . $lastColumn . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getStyle('A3:'. $lastColumn . '4')->getFont()->setItalic(true);
        $sheet->getStyle('A3:'. $lastColumn . '4')->getFont()->setSize(11);


        $planPaymentTypes = $this->receivePlanService->getPlanPaymentTypes();
        $planPayments = TaxPlanItem::where('paid', false)->get();
        $reasons = ReceivePlan::getReasons();
        $periods = $this->receivePlanService->getPeriods();
        $plans = $this->receivePlanService->getPlans(null, $periods[0]['start'], end($periods)['start']);

        $activeObjectIds = BObject::active()->orderBy('code')->pluck('id')->toArray();
        $closedObjectIds = ReceivePlan::whereBetween('date', [$periods[0]['start'], end($periods)['start']])->groupBy('object_id')->pluck('object_id')->toArray();

        $objects = BObject::whereIn('id', array_merge($activeObjectIds, $closedObjectIds))->get();

        $columnIndex = 2;
        foreach($periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $period['format']);
            $sheet->getColumnDimension($column)->setWidth(30);
            $columnIndex++;
        }

        $sheet->setCellValue($lastColumn . '1', 'ИТОГО');

        $total = 0;
        $targetAvansTotal = 0;
        $otherTotal = 0;
        $columnIndex = 2;
        foreach($periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $plans->where('date', $period['start'])->sum('amount');
            $targetAvansAmount = $plans->where('date', $period['start'])->where('reason_id', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            $total += $amount;
            $targetAvansTotal += $targetAvansAmount;
            $otherTotal += $otherAmount;

            $sheet->setCellValue($column . 2, $amount != 0 ? $amount : '');
            $sheet->setCellValue($column . 3, $targetAvansAmount != 0 ? $targetAvansAmount : '');
            $sheet->setCellValue($column . 4, $otherAmount != 0 ? $otherAmount : '');

            $columnIndex++;
        }

        $sheet->setCellValue($lastColumn . 2, $total != 0 ? $total : '');
        $sheet->setCellValue($lastColumn . 3, $targetAvansTotal != 0 ? $targetAvansTotal : '');
        $sheet->setCellValue($lastColumn . 4, $otherTotal != 0 ? $otherTotal : '');

        $row = 5;
        foreach($objects as $object) {

            $total = 0;
            foreach($periods as $period) {
                $total += $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
            }

            if ($total == 0) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $object->name);
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

            $columnIndex = 2;
            foreach($periods as $period) {

                $column = $this->getColumnWord($columnIndex);
                $amount = $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');

                $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');

                $columnIndex++;
            }

            $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
            $row++;

            foreach($reasons as $reasonId => $reason) {
                $total = $plans->where('object_id', $object->id)->where('reason_id', $reasonId)->sum('amount');

                if ($total == 0) {
                    continue;
                }

                $sheet->setCellValue('A' . $row, '         ' . $reason);
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->getStyle('A' . $row . ':'. $lastColumn . $row)->getFont()->setItalic(true);
                $sheet->getStyle('A' . $row . ':'. $lastColumn . $row)->getFont()->setSize(11);

                $total = 0;
                $columnIndex = 2;
                foreach($periods as $period) {

                    $column = $this->getColumnWord($columnIndex);
                    $plan = $plans->where('object_id', $object->id)->where('date', $period['start'])->where('reason_id', $reasonId)->first();
                    $amount = 0;

                    if ($plan) {
                        $amount = $plan->amount;
                    }


                    $total += $amount;

                    $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
                    $columnIndex++;
                }

                $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
                $row++;
            }
        }

        $sheet->getRowDimension($row)->setRowHeight(5);
        $row++;

        foreach($planPaymentTypes as $type) {
            $sheet->setCellValue('A' . $row, $type);
            $sheet->getRowDimension($row)->setRowHeight(30);

            $total = 0;
            $columnIndex = 2;
            foreach($periods as $index => $period) {

                $column = $this->getColumnWord($columnIndex);
                if ($index === 0) {
                    $amount = $planPayments->where('name', $type)->where('due_date', '<=', $period['end'])->sum('amount');
                } else {
                    $amount = $planPayments->where('name', $type)->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
                }
                $total += $amount;

                $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
                $columnIndex++;
            }

            $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
            $row++;
        }

        $sheet->getRowDimension($row)->setRowHeight(5);
        $row++;

        $sheet->setCellValue('A' . $row, 'Итого расходов по неделям:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $total = 0;
        $columnIndex = 2;
        foreach($periods as $index => $period) {

            $column = $this->getColumnWord($columnIndex);
            if ($index === 0) {
                $amount = $planPayments->where('due_date', '<=', $period['end'])->sum('amount');
            } else {
                $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
            }

            $total += $amount;

            $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
            $columnIndex++;
        }

        $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
        $row++;

        $sheet->setCellValue('A' . $row, 'Итого расходов по месяцам:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $row++;
        $sheet->setCellValue('A' . $row, 'Сальдо (без учета целевых авансов) по неделям:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $columnIndex = 2;
        foreach($periods as $index => $period) {

            $column = $this->getColumnWord($columnIndex);
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $planPayments->where('due_date', '<=', $period['end'])->sum('amount');
            } else {
                $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
            }

            $diff = $otherAmount - $amount;

            $sheet->setCellValue($column . $row, $diff != 0 ? $diff : '');
            $sheet->getStyle($column . $row)->getFont()->setColor(new Color($diff < 0 ? Color::COLOR_RED : Color::COLOR_BLACK));

            $columnIndex++;
        }

        $row++;
        $sheet->setCellValue('A' . $row, 'Сальдо (без учета целевых авансов) по неделям:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $prev = 0;
        $columnIndex = 2;
        foreach($periods as $index => $period) {

            $column = $this->getColumnWord($columnIndex);
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $planPayments->where('due_date', '<=', $period['end'])->sum('amount');
            } else {
                $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
            }

            $diff = $otherAmount - $amount + $prev;
            $prev = $diff;

            $sheet->setCellValue($column . $row, $diff != 0 ? $diff : '');
            $sheet->getStyle($column . $row)->getFont()->setColor(new Color($diff < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex++;
        }


        $sheet->getStyle('A1:' . 'A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('B2:' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('B' . ($row - 1) . ':' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('A' . ($row - 3) . ':' . $lastColumn . $row)->getFont()->setBold(true);

        $sheet->getStyle('B2:' . $lastColumn . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A' . ($row - 3) . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'c7c7c7']]]
        ]);
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
