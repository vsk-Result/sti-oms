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

class PivotObjectSheet implements
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
        return 'Сводная по объектам';
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Период с ' . Carbon::parse((clone $this->payments)->min('date'))->format('d.m.Y') . ' по ' . Carbon::parse((clone $this->payments)->max('date'))->format('d.m.Y'));

        $sheet->setCellValue('A2', 'Объект');
        $sheet->setCellValue('B2', 'Приход');
        $sheet->setCellValue('C2', 'Расход');
        $sheet->setCellValue('D2', 'Сальдо');

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);

        $sheet->getStyle('A1:D2')->getFont()->setBold(true);
        $sheet->mergeCells('A1:D1');

        $objectIds = (clone $this->payments)->where('type_id', Payment::TYPE_OBJECT)->groupBy('object_id')->pluck('object_id')->toArray();
        $objects = BObject::whereIn('id', $objectIds)->orderByDesc('code')->get();

        $row = 3;
        $sum = [
            'total' => 0,
            'receive' => 0,
            'payment' => 0,
        ];
        foreach ($objects as $object) {
            $objectId = $object->id;
            $objectName = $object ? $object->getName() : 'Не определен_' . $objectId;

            $total = (clone $this->payments)->where('type_id', Payment::TYPE_OBJECT)->where('object_id', $objectId)->sum('amount');
            $receive = (clone $this->payments)->where('type_id', Payment::TYPE_OBJECT)->where('object_id', $objectId)->where('amount', '>=', 0)->sum('amount');
            $payment = $total - $receive;

            $sum['total'] += $total;
            $sum['receive'] += $receive;
            $sum['payment'] += $payment;

            $sheet = $this->fillRow($sheet, $row++, [
                'object_name' => $objectName,
                'receive' => $receive,
                'payment' => $payment,
                'total' => $total,
            ]);
        }

        $total = (clone $this->payments)->where('type_id', Payment::TYPE_TRANSFER)->sum('amount');
        $receive = (clone $this->payments)->where('type_id', Payment::TYPE_TRANSFER)->where('amount', '>=', 0)->sum('amount');
        $payment = $total - $receive;

        $sum['total'] += $total;
        $sum['receive'] += $receive;
        $sum['payment'] += $payment;

        $sheet = $this->fillRow($sheet, $row++, [
            'object_name' => 'Трансфер',
            'receive' => $receive,
            'payment' => $payment,
            'total' => $total,
        ]);

        $total = (clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->sum('amount');
        $receive = (clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->where('amount', '>=', 0)->sum('amount');
        $payment = $total - $receive;

        $sum['total'] += $total;
        $sum['receive'] += $receive;
        $sum['payment'] += $payment;

        $sheet = $this->fillRow($sheet, $row++, [
            'object_name' => 'Общие затраты',
            'receive' => $receive,
            'payment' => $payment,
            'total' => $total,
        ]);

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, $sum['receive']);
        $sheet->setCellValue('C' . $row, $sum['payment']);
        $sheet->setCellValue('D' . $row, $sum['total']);

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('C' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($sum['total'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $sheet->getStyle('A1:D' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:D' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A2:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);

        $sheet->getStyle('B3:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }

    private function fillRow($sheet, $row, $data)
    {
        $sheet->setCellValue('A' . $row, $data['object_name']);
        $sheet->setCellValue('B' . $row, $data['receive']);
        $sheet->setCellValue('C' . $row, $data['payment']);
        $sheet->setCellValue('D' . $row, $data['total']);

        $sheet->getRowDimension($row)->setRowHeight(30);

        $sheet->getStyle('B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('C' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($data['total'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        return $sheet;
    }
}
