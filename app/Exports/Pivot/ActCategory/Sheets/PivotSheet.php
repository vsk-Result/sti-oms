<?php

namespace App\Exports\Pivot\ActCategory\Sheets;

use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ActService;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function title(): string
    {
        return 'Отчет';
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);

        $sheet->getStyle('A1:J2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A5')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->setCellValue('A1', 'Категория');
        $sheet->setCellValue('B1', 'Сумма по договору');
        $sheet->setCellValue('C1', '% по договору');
        $sheet->setCellValue('D1', 'Итого выполнение');
        $sheet->setCellValue('E1', '% выполнение');
        $sheet->setCellValue('F1', 'Остаток к выполнению');
        $sheet->setCellValue('G1', 'Оплата');
        $sheet->setCellValue('H1', '% оплаты');
        $sheet->setCellValue('I1', 'Остаток к оплате');
        $sheet->setCellValue('J1', '% остатка к оплате');

//        $sheet->getStyle('A3:'. $lastColumn . '4')->getFont()->setItalic(true);

        $total = [];

        $filteredObjects = BObject::active()->orderBy('code')->get();
        $activeObjectIds = $filteredObjects->pluck('id')->toArray();
        $activeObjects = BObject::whereIn('id', $activeObjectIds)->orderByDesc('code')->get();
        $acts = $this->actService->filterActs(['object_id' => $activeObjectIds], $total);

        $sheet->setCellValue( 'A2', 'Итого');
        $sheet->setCellValue( 'A3', 'Материалы');
        $sheet->setCellValue( 'A4', 'Работы');
        $sheet->setCellValue( 'A5', 'Накладные');

        $totalMaterialAmount = $acts->sum('amount');
        $totalRadAmount = $acts->sum('rad_amount');
        $totalOpsteAmount = $acts->sum('opste_amount');

        $totalMaterialContractAmount = 0;
        $totalRadContractAmount = 0;
        $totalOpsteContractAmount = 0;

        foreach (Contract::whereIn('object_id', $activeObjectIds)->where('type_id', Contract::TYPE_MAIN)->get() as $contract) {
            $totalMaterialContractAmount += $contract->getMaterialAmount();
            $totalRadContractAmount += $contract->getRadAmount();
            $totalOpsteContractAmount += $contract->getOpsteAmount();
        }

        $totalMaterialPaidAmount = 0;
        $totalRadPaidAmount = 0;
        $totalOpstePaidAmount = 0;

        foreach ($activeObjects as $aobject) {
            $receivePayments = $aobject->payments()
                ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                ->where('company_id', 1)
                ->whereIn('organization_sender_id', $aobject->customers->pluck('id')->toArray())
                ->get();

            $totalMaterialPaidAmount += $receivePayments->where('category', Payment::CATEGORY_MATERIAL)->sum('amount');
            $totalRadPaidAmount += $receivePayments->where('category', Payment::CATEGORY_RAD)->sum('amount');
            $totalOpstePaidAmount += $receivePayments->where('category', Payment::CATEGORY_OPSTE)->sum('amount');
        }

        $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
        $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
        $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

        $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
        $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
        $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;
        $totalContractAmount = $totalMaterialContractAmount + $totalRadContractAmount + $totalOpsteContractAmount;


        $sheet->setCellValue('B2', $totalContractAmount);
        $sheet->setCellValue('C2', 1);
        $sheet->setCellValue('D2', $totalAmount);
        $sheet->setCellValue('E2', $totalContractAmount != 0 ? $totalAmount / $totalContractAmount : 0);
        $sheet->setCellValue('F2', $totalContractAmount - $totalAmount);
        $sheet->setCellValue('G2', $totalPaidAmount);
        $sheet->setCellValue('H2', $totalContractAmount != 0 ? $totalPaidAmount / $totalContractAmount : 0);
        $sheet->setCellValue('I2', $totalLeftPaidAmount);
        $sheet->setCellValue('J2', $totalContractAmount != 0 ? $totalLeftPaidAmount / $totalContractAmount : 0);

        $sheet->setCellValue('B3', $totalMaterialContractAmount);
        $sheet->setCellValue('C3', $totalContractAmount != 0 ? $totalMaterialContractAmount / $totalContractAmount : 0);
        $sheet->setCellValue('D3', $totalMaterialAmount);
        $sheet->setCellValue('E3', $totalMaterialContractAmount != 0 ? $totalMaterialAmount / $totalMaterialContractAmount : 0);
        $sheet->setCellValue('F3', $totalMaterialContractAmount - $totalMaterialAmount);
        $sheet->setCellValue('G3', $totalMaterialPaidAmount);
        $sheet->setCellValue('H3', $totalPaidAmount != 0 ? $totalMaterialPaidAmount / $totalPaidAmount : 0);
        $sheet->setCellValue('I3', $totalMaterialLeftPaidAmount);
        $sheet->setCellValue('J3', $totalLeftPaidAmount != 0 ? $totalMaterialLeftPaidAmount / $totalLeftPaidAmount : 0);

        $sheet->setCellValue('B4', $totalRadContractAmount);
        $sheet->setCellValue('C4', $totalContractAmount != 0 ? $totalRadContractAmount / $totalContractAmount : 0);
        $sheet->setCellValue('D4', $totalRadAmount);
        $sheet->setCellValue('E4', $totalRadContractAmount != 0 ? $totalRadAmount / $totalRadContractAmount : 0);
        $sheet->setCellValue('F4', $totalRadContractAmount - $totalRadAmount);
        $sheet->setCellValue('G4', $totalRadPaidAmount);
        $sheet->setCellValue('H4', $totalPaidAmount != 0 ? $totalRadPaidAmount / $totalPaidAmount : 0);
        $sheet->setCellValue('I4', $totalRadLeftPaidAmount);
        $sheet->setCellValue('J4', $totalLeftPaidAmount != 0 ? $totalRadLeftPaidAmount / $totalLeftPaidAmount : 0);

        $sheet->setCellValue('B5', $totalOpsteContractAmount);
        $sheet->setCellValue('C5', $totalContractAmount != 0 ? $totalOpsteContractAmount / $totalContractAmount : 0);
        $sheet->setCellValue('D5', $totalOpsteAmount);
        $sheet->setCellValue('E5', $totalOpsteContractAmount != 0 ? $totalOpsteAmount / $totalOpsteContractAmount : 0);
        $sheet->setCellValue('F5', $totalOpsteContractAmount - $totalOpsteAmount);
        $sheet->setCellValue('G5', $totalOpstePaidAmount);
        $sheet->setCellValue('H5', $totalPaidAmount != 0 ? $totalOpstePaidAmount / $totalPaidAmount : 0);
        $sheet->setCellValue('I5', $totalOpsteLeftPaidAmount);
        $sheet->setCellValue('J5', $totalLeftPaidAmount != 0 ? $totalOpsteLeftPaidAmount / $totalLeftPaidAmount : 0);

        $row = 6;
        foreach($activeObjects as $object) {
            $acts = $object->acts;
            $totalMaterialAmount = $acts->sum('amount');
            $totalRadAmount = $acts->sum('rad_amount');
            $totalOpsteAmount = $acts->sum('opste_amount');

            $totalMaterialContractAmount = 0;
            $totalRadContractAmount = 0;
            $totalOpsteContractAmount = 0;

            foreach ($object->contracts()->where('type_id', Contract::TYPE_MAIN)->get() as $contract) {
                $totalMaterialContractAmount += $contract->getMaterialAmount();
                $totalRadContractAmount += $contract->getRadAmount();
                $totalOpsteContractAmount += $contract->getOpsteAmount();
            }

            $receivePayments = $object->payments()
                ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                ->where('company_id', 1)
                ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                ->get();

            $totalMaterialPaidAmount = $receivePayments->where('category', Payment::CATEGORY_MATERIAL)->sum('amount');
            $totalRadPaidAmount = $receivePayments->where('category',Payment::CATEGORY_RAD)->sum('amount');
            $totalOpstePaidAmount = $receivePayments->where('category', Payment::CATEGORY_OPSTE)->sum('amount');

            $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
            $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
            $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

            $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
            $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
            $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;
            $totalContractAmount = $totalMaterialContractAmount + $totalRadContractAmount + $totalOpsteContractAmount;


            $sheet->getRowDimension($row)->setRowHeight(40);
            $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');


            $sheet->setCellValue('A' . $row, $object->getName());
            $sheet->setCellValue('B' . $row, $totalContractAmount);
            $sheet->setCellValue('C' . $row, 1);
            $sheet->setCellValue('D' . $row, $totalAmount);
            $sheet->setCellValue('E' . $row, $totalContractAmount != 0 ? $totalAmount / $totalContractAmount : 0);
            $sheet->setCellValue('F' . $row, $totalContractAmount - $totalAmount);
            $sheet->setCellValue('G' . $row, $totalPaidAmount);
            $sheet->setCellValue('H' . $row, $totalContractAmount != 0 ? $totalPaidAmount / $totalContractAmount : 0);
            $sheet->setCellValue('I' . $row, $totalLeftPaidAmount);
            $sheet->setCellValue('J' . $row, $totalContractAmount != 0 ? $totalLeftPaidAmount / $totalContractAmount : 0);

            $row++;
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('A' . $row . ':A' . $row)->getFont()->setItalic(true);
            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);

            $sheet->setCellValue('A' . $row, 'Материалы');
            $sheet->setCellValue('B' . $row, $totalMaterialContractAmount);
            $sheet->setCellValue('C' . $row, $totalContractAmount != 0 ? $totalMaterialContractAmount / $totalContractAmount : 0);
            $sheet->setCellValue('D' . $row, $totalMaterialAmount);
            $sheet->setCellValue('E' . $row, $totalMaterialContractAmount != 0 ? $totalMaterialAmount / $totalMaterialContractAmount : 0);
            $sheet->setCellValue('F' . $row, $totalMaterialContractAmount - $totalMaterialAmount);
            $sheet->setCellValue('G' . $row, $totalMaterialPaidAmount);
            $sheet->setCellValue('H' . $row, $totalPaidAmount != 0 ? $totalMaterialPaidAmount / $totalPaidAmount : 0);
            $sheet->setCellValue('I' . $row, $totalMaterialLeftPaidAmount);
            $sheet->setCellValue('J' . $row, $totalLeftPaidAmount != 0 ? $totalMaterialLeftPaidAmount / $totalLeftPaidAmount : 0);

            $row++;
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('A' . $row . ':A' . $row)->getFont()->setItalic(true);
            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);

            $sheet->setCellValue('A' . $row, 'Работы');
            $sheet->setCellValue('B' . $row, $totalRadContractAmount);
            $sheet->setCellValue('C' . $row, $totalContractAmount != 0 ? $totalRadContractAmount / $totalContractAmount : 0);
            $sheet->setCellValue('D' . $row, $totalRadAmount);
            $sheet->setCellValue('E' . $row, $totalRadContractAmount != 0 ? $totalRadAmount / $totalRadContractAmount : 0);
            $sheet->setCellValue('F' . $row, $totalRadContractAmount - $totalRadAmount);
            $sheet->setCellValue('G' . $row, $totalRadPaidAmount);
            $sheet->setCellValue('H' . $row, $totalPaidAmount != 0 ? $totalRadPaidAmount / $totalPaidAmount : 0);
            $sheet->setCellValue('I' . $row, $totalRadLeftPaidAmount);
            $sheet->setCellValue('J' . $row, $totalLeftPaidAmount != 0 ? $totalRadLeftPaidAmount / $totalLeftPaidAmount : 0);

            $row++;
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('A' . $row . ':A' . $row)->getFont()->setItalic(true);
            $sheet->getRowDimension($row)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);

            $sheet->setCellValue('A' . $row, 'Накладные');
            $sheet->setCellValue('B' . $row, $totalOpsteContractAmount);
            $sheet->setCellValue('C' . $row, $totalContractAmount != 0 ? $totalOpsteContractAmount / $totalContractAmount : 0);
            $sheet->setCellValue('D' . $row, $totalOpsteAmount);
            $sheet->setCellValue('E' . $row, $totalOpsteContractAmount != 0 ? $totalOpsteAmount / $totalOpsteContractAmount : 0);
            $sheet->setCellValue('F' . $row, $totalOpsteContractAmount - $totalOpsteAmount);
            $sheet->setCellValue('G' . $row, $totalOpstePaidAmount);
            $sheet->setCellValue('H' . $row, $totalPaidAmount != 0 ? $totalOpstePaidAmount / $totalPaidAmount : 0);
            $sheet->setCellValue('I' . $row, $totalOpsteLeftPaidAmount);
            $sheet->setCellValue('J' . $row, $totalLeftPaidAmount != 0 ? $totalOpsteLeftPaidAmount / $totalLeftPaidAmount : 0);

            $row++;
        }

        $row--;

        $sheet->getStyle('B2:B' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('D2:D' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('F2:F' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('I2:I' . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);;
        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);;
        $sheet->getStyle('H2:H' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);;
        $sheet->getStyle('J2:J' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);;

        $sheet->getStyle('A1:J' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'b7b7b7']]]
        ]);
        $sheet->getStyle('A2:J2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('B2:J' . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A2:A' . $row)->getAlignment()->setVertical('center');

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, 10, $row);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
    }
}
