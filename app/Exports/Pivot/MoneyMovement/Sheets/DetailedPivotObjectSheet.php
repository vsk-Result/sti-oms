<?php

namespace App\Exports\Pivot\MoneyMovement\Sheets;

use App\Models\Object\BObject;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailedPivotObjectSheet implements
    WithTitle,
    WithStyles
{
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $this->payments = $payments;
    }

    public function title(): string
    {
        return 'Детализированная сводная по объектам';
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $minDate = (clone $this->payments)->min('date');
        $maxDate = (clone $this->payments)->max('date');

        $sheet->setCellValue('A2', 'Период с ' . Carbon::parse($minDate)->format('d.m.Y') . ' по ' . Carbon::parse($maxDate)->format('d.m.Y'));

        $sheet->setCellValue('A3', 'Остаток на начало ' . Carbon::parse($minDate)->format('d.m'));
        $sheet->setCellValue('B3', Payment::where('date', '<=', $minDate)->sum('amount'));

        $sheet->setCellValue('A4', 'Итого приход');
        $sheet->setCellValue('B4', Payment::whereBetween('date', [$minDate, $maxDate])->where('amount', '>=', 0)->sum('amount'));

        $sheet->setCellValue('A5', 'Итого расход');
        $sheet->setCellValue('B5', Payment::whereBetween('date', [$minDate, $maxDate])->where('amount', '<', 0)->sum('amount'));

        $sheet->setCellValue('A6', 'Остаток на конец ' . Carbon::parse($maxDate)->format('d.m'));
        $sheet->setCellValue('B6', Payment::where('date', '<=', $maxDate)->sum('amount'));

        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(26);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);

        $sheet->getStyle('A1:D7')->getFont()->setBold(true);
        $sheet->mergeCells('A2:D2');

        $objectIds = (clone $this->payments)->where('type_id', Payment::TYPE_OBJECT)->groupBy('object_id')->pluck('object_id')->toArray();
        $objects = BObject::whereIn('id', $objectIds)->orderByDesc('code')->get();

        $row = 9;
        $totals = [
            'receive' => 0,
            'payment' => 0,
        ];

        foreach ($objects as $object) {
            if ($object->code === '27.1') {
                continue;
            }

            $receive = $object->payments()->where('amount', '>=', 0)->sum('amount');
            $payment = $object->payments()->where('amount', '<', 0)->sum('amount');

            $totals['receive'] += $receive;
            $totals['payment'] += $payment;

            $sheet->setCellValue('A' . $row, $object->getName());
            $row++;
            $sheet->setCellValue('A' . $row, 'Приход');
            $sheet->setCellValue('B' . $row, $receive);
            $row++;
            $sheet->setCellValue('A' . $row, 'Расход');
            $sheet->setCellValue('B' . $row, $payment);
            $row++;
            $sheet->setCellValue('A' . $row, 'Сальдо');
            $sheet->setCellValue('B' . $row, $receive + $payment);
            $row++;

//            $sheet->getRowDimension($row)->setRowHeight(30);
        }

        $sheet->setCellValue('A' . $row, 'Офис');
        $row++;

        $row++;
        $sheet->setCellValue('A' . $row, 'Общие затраты');
    }
}
