<?php

namespace App\Exports\Pivot\MoneyMovement\Sheets;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotOrganizationReceiveSheet implements
    WithTitle,
    WithStyles
{
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $companyOrganization = Organization::where('company_id', 1)
            ->where('name', 'ООО "Строй Техно Инженеринг"')
            ->first();

        $this->payments = $payments->where('organization_sender_id', '!=', $companyOrganization->id);
    }

    public function title(): string
    {
        return 'Сводная по контрагентам (приходы)';
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Контрагент');
        $sheet->setCellValue('B1', 'Приход');

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(22);

        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        $total = 0;
        $info = [];
        foreach ((clone $this->payments)->select('organization_sender_id', DB::raw('sum(amount) as sum_amount'))->groupBy('organization_sender_id')->get() as $payment) {
            $info[Organization::find($payment->organization_sender_id)?->name ?? 'Не определена'] = $payment->sum_amount;
            $total += $payment->sum_amount;
        }

        arsort($info);

        $row = 2;
        foreach ($info as $organizationName => $amount) {

            $sheet->setCellValue('A' . $row, $organizationName);
            $sheet->setCellValue('B' . $row, $amount);

            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, $total);

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setColor(new Color(Color::COLOR_DARKGREEN));

        $sheet->getStyle('A1:B' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);

        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);

        $sheet->getStyle('B2:B' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    }
}
