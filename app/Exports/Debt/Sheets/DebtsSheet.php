<?php

namespace App\Exports\Debt\Sheets;

use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Models\Organization;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DebtsSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private BObject $object;

    public function __construct(BObject $object)
    {
        $this->object = $object;
    }

    public function title(): string
    {
        return 'Детализация долгов';
    }

    public function styles(Worksheet $sheet): void
    {
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Объект');
        $sheet->setCellValue('B1', 'Тип');
        $sheet->setCellValue('C1', 'Категория');
        $sheet->setCellValue('D1', 'Заказ сделал');
        $sheet->setCellValue('E1', 'Контрагент');
        $sheet->setCellValue('F1', 'Описание');
        $sheet->setCellValue('G1', 'Счет');
        $sheet->setCellValue('H1', 'Сумма счета');
        $sheet->setCellValue('I1', 'Сумма оплаты');
        $sheet->setCellValue('J1', 'Долг');
        $sheet->setCellValue('K1', 'Срок оплаты счета');
        $sheet->setCellValue('L1', 'Комментарий');

        $sheet->getRowDimension(1)->setRowHeight(35);

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(13);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(23);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('F')->setWidth(80);
        $sheet->getColumnDimension('G')->setWidth(33);
        $sheet->getColumnDimension('H')->setWidth(17);
        $sheet->getColumnDimension('I')->setWidth(17);
        $sheet->getColumnDimension('J')->setWidth(17);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(35);

        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $row = 2;
        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();
        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();

        $debtsObjectImport = $debtObjectImport->debts()->with('organization', 'object')->get();

        $debts = $this->object
            ->debts()
            ->whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id, $debtObjectImport?->id])
            ->orderBy(Organization::select('name')->whereColumn('organizations.id', 'debts.organization_id'))
            ->with('organization', 'object')
            ->orderBy('amount')
            ->get();

        foreach ($debts as $debt) {
            $isContractor = $debt->type_id === Debt::TYPE_CONTRACTOR;
            $objectExistInObjectImport = $debtsObjectImport->where('object_id', $debt->object_id)->first();

            if ($objectExistInObjectImport && $isContractor && $debt->import_id !== $debtObjectImport->id) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $debt->getObject());
            $sheet->setCellValue('B' . $row, $debt->getType());
            $sheet->setCellValue('C' . $row, $debt->category);
            $sheet->setCellValue('D' . $row, $debt->order_author);
            $sheet->setCellValue('E' . $row, $debt->organization?->name);
            $sheet->setCellValue('F' . $row, $debt->description);
            $sheet->setCellValue('G' . $row, $debt->invoice_number);
            $sheet->setCellValue('H' . $row, $debt->invoice_amount);
            $sheet->setCellValue('I' . $row, ($debt->invoice_amount + $debt->amount));
            $sheet->setCellValue('J' . $row, $debt->amount);
            $sheet->setCellValue('K' . $row, Date::dateTimeToExcel(Carbon::parse($debt->invoice_payment_due_date)));
            $sheet->setCellValue('L' . $row, $debt->comment);

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }
        $row--;

        $sheet->getStyle('A1:L' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:L1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('J2:J' . $row)->getFont()->setColor(new Color(Color::COLOR_RED));
        $sheet->getStyle('H2:J' . $row)->getNumberFormat()->setFormatCode('_-* #,##0_-;-* #,##0_-;_-* "-"_-;_-@_-');
        $sheet->getStyle('K2:K' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

        $sheet->setAutoFilter('A1:L' . $row);
    }
}
