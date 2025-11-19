<?php

namespace App\Exports\Pivot\MoneyMovement\Sheets;

use App\Models\Object\BObject;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
        $period = [$minDate, $maxDate];

        $sheet->setCellValue('A2', 'Период с ' . Carbon::parse($minDate)->format('d.m.Y') . ' по ' . Carbon::parse($maxDate)->format('d.m.Y'));

        $sheet->setCellValue('A3', 'Остаток на начало ' . Carbon::parse($minDate)->format('d.m'));
        $sheet->setCellValue('B3', Payment::where('date', '<=', $minDate)->sum('amount'));

        $sheet->setCellValue('A4', 'Итого приход');
        $sheet->setCellValue('B4', Payment::whereBetween('date', $period)->where('amount', '>=', 0)->sum('amount'));

        $sheet->setCellValue('A5', 'Итого расход');
        $sheet->setCellValue('B5', Payment::whereBetween('date', $period)->where('amount', '<', 0)->sum('amount'));

        $sheet->setCellValue('A6', 'Остаток на конец ' . Carbon::parse($maxDate)->format('d.m'));
        $sheet->setCellValue('B6', Payment::where('date', '<=', $maxDate)->sum('amount'));

        $sheet->getStyle('A2:B2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(26);
        $sheet->getColumnDimension('B')->setWidth(17);

        $sheet->getStyle('A1:B7')->getFont()->setBold(true);
        $sheet->mergeCells('A2:B2');

        $sheet->getStyle('A2:B2')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $objectIds = (clone $this->payments)->where('type_id', Payment::TYPE_OBJECT)->groupBy('object_id')->pluck('object_id')->toArray();
        $objects = BObject::whereIn('id', $objectIds)->orderByDesc('code')->get();

        $row = 9;

        foreach ($objects as $object) {
            if ($object->code === '27.1') {
                continue;
            }

            $groupInfo = [];

            foreach ($object->payments()->whereBetween('date', $period)->where('amount', '<', 0)->select('category', DB::raw('sum(amount) as sum_amount'))->groupBy('category')->get() as $payment) {
                $groupInfo[] = [
                    'name' => $payment->category,
                    'amount' => $payment->sum_amount
                ];
            }

            $this->fillObjectInfo($sheet, $row, [
                'title' => $object->getName(),
                'receive' => $object->payments()->whereBetween('date', $period)->where('amount', '>=', 0)->sum('amount'),
                'payment' => $object->payments()->whereBetween('date', $period)->where('amount', '<', 0)->sum('amount'),
                'period' => $period,
                'groupInfo' => $groupInfo
            ]);

            $row += 4 + count($groupInfo);
        }

        $row++;

        $office = BObject::where('code', '27.1')->first();

        $this->fillObjectInfo($sheet, $row, [
            'title' => 'Офис',
            'receive' => $office->payments()->whereBetween('date', $period)->where('amount', '>=', 0)->sum('amount'),
            'payment' => $office->payments()->whereBetween('date', $period)->where('amount', '<', 0)->sum('amount'),
            'period' => $period,
        ]);

        $row += 5;

        $this->fillObjectInfo($sheet, $row, [
            'title' => 'Общие затраты',
            'receive' => (clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->where('amount', '>=', 0)->sum('amount'),
            'payment' => (clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->where('amount', '<', 0)->sum('amount'),
            'period' => $period,
        ]);

        $row += 4;

        $sheet->getStyle('B3:B' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }

    public function fillObjectInfo(&$sheet, $row, array $info)
    {
        $sheet->getStyle('A' . $row . ':B' . ($row + 3 + count($info['groupInfo'] ?? [])))->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('A' . $row . ':B' . ($row + 3 + count($info['groupInfo'] ?? [])))->getFont()->setName('Calibri')->setSize(10);

        $sheet->setCellValue('A' . $row, $info['title']);

        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Приход');
        $sheet->setCellValue('B' . $row, $info['receive']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Расход');
        $sheet->setCellValue('B' . $row, $info['payment']);
        $row++;

        if (isset($info['groupInfo'])) {
            foreach ($info['groupInfo'] as $groupInfo) {
                $sheet->setCellValue('A' . $row, '    ' . $groupInfo['name']);
                $sheet->setCellValue('B' . $row, $groupInfo['amount']);

                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setItalic(true);

                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                $row++;
            }
        }

        $sheet->setCellValue('A' . $row, 'Сальдо');
        $sheet->setCellValue('B' . $row, $info['receive'] + $info['payment']);
    }
}
