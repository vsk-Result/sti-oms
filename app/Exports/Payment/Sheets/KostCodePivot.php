<?php

namespace App\Exports\Payment\Sheets;

use App\Models\KostCode;
use App\Models\Object\BObject;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KostCodePivot implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private Builder $payments;

    private BObject | null $object;

    public function __construct(string $sheetName, Builder $payments, BObject | null $object)
    {
        $this->sheetName = $sheetName;
        $this->payments = $payments;
        $this->object = $object;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Статья');
        $sheet->setCellValue('B1', 'Приходы');
        $sheet->setCellValue('C1', 'Расходы');
        $sheet->setCellValue('D1', 'Общая сумма');
        if (!$this->object) {
            $sheet->setCellValue('E1', 'Объект');
        }

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(65);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);
        if (!$this->object) {
            $sheet->getColumnDimension('E')->setWidth(15);
        }

        $lastColumn = $this->object ? 'D' : 'E';

        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);

        $row = 2;
        $codesWithId = KostCode::getCodesWithId();

        $groupedByCodePayments = (clone $this->payments)->get()->sort(function($a, $b) use($codesWithId) {
            return ($codesWithId[$a->code] ?? 0) - ($codesWithId[$b->code] ?? 0);
        })->groupBy('code');

        $sumPay = 0;
        $sumReceive = 0;

        foreach ($groupedByCodePayments as $code => $codePayments) {
            foreach ($codePayments->groupBy('object_id') as $objectId => $objectPayments) {
                $object = BObject::find($objectId);

                $total = $objectPayments->sum('amount');

                $receive = $objectPayments->where('amount', '>=', 0)->sum('amount');
                $pay = $objectPayments->where('amount', '<', 0)->sum('amount');

                $sheet->setCellValue('A' . $row, KostCode::getTitleByCode($code));
                $sheet->setCellValue('B' . $row, $receive);
                $sheet->setCellValue('C' . $row, $pay);
                $sheet->setCellValue('D' . $row, $total);

                $sumPay += $pay;
                $sumReceive += $receive;

                if (!$this->object) {
                    $sheet->setCellValue('E' . $row, $object->code ?? '');
                }

                $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($total < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                $sheet->getRowDimension($row)->setRowHeight(30);
                $row++;
            }
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, $sumReceive);
        $sheet->setCellValue('C' . $row, $sumPay);
        $sheet->setCellValue('D' . $row, $sumReceive + $sumPay);

        $sheet->getStyle('D' . $row)->getFont()->setColor(new Color(($sumReceive + $sumPay) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $sheet->getStyle('B2:B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
        $sheet->getStyle('C2:C' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));

        $sheet->getStyle('A1:' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B1:' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);

        $sheet->getStyle('B2:D' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

    }
}
