<?php

namespace App\Exports\Object\Report\ActCategory\Sheets;

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
    private BObject $object;
    private ActService $actService;

    public function __construct(ActService $actService, BObject $object)
    {
        $this->actService = $actService;
        $this->object = $object;
    }

    public function title(): string
    {
        return 'Отчет';
    }

    public function styles(Worksheet $sheet): void
    {
        $object = $this->object;
        $total = [];
        $acts = $this->actService->filterActs(['object_id' => [$object->id]], $total, false);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(30);

        $lastColumnIndex = $acts->count() + 10;
        $lastColumn = $this->getColumnWord($lastColumnIndex);

        for ($i = 1; $i <= $lastColumnIndex; $i++) {
            $column = $this->getColumnWord($i);
            $sheet->getColumnDimension($column)->setWidth(30);
        }

        $sheet->getStyle('A1:' . $lastColumn . '2')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->setCellValue('A1', 'Категория');
        $sheet->setCellValue('B1', 'Сумма по договору');
        $sheet->setCellValue('C1', '% по договору');

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $this->getColumnWord($colIndex++);
            $sheet->setCellValue($column . '1', 'Акт ' . $act->number);
        }

        $sheet->setCellValue($this->getColumnWord($colIndex++) . '1', 'Итого выполнение');
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '1', '% выполнение');
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '1', 'Остаток к выполнению');
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '1', 'Оплата');
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '1', '% оплаты');
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '1', 'Остаток к оплате');
        $sheet->setCellValue($this->getColumnWord($colIndex) . '1', '% остатка к оплате');

        $sheet->getStyle('A3:A5')->getFont()->setItalic(true);
        $sheet->getStyle('A2:' . $lastColumn . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->setCellValue( 'A2', $object->getName());
        $sheet->setCellValue( 'A3', 'Материалы');
        $sheet->setCellValue( 'A4', 'Работы');
        $sheet->setCellValue( 'A5', 'Накладные');

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
        $totalRadPaidAmount = $receivePayments->where('category', Payment::CATEGORY_RAD)->sum('amount');
        $totalOpstePaidAmount = $receivePayments->where('category', Payment::CATEGORY_OPSTE)->sum('amount');

        $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
        $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
        $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

        $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
        $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
        $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;
        $totalContractAmount = $totalMaterialContractAmount + $totalRadContractAmount + $totalOpsteContractAmount;


        $sheet->setCellValue('B2', $totalContractAmount);
        $sheet->setCellValue('C2', 1);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $this->getColumnWord($colIndex++);
            $sheet->setCellValue($column . '2', $act->getAmount());
            $sheet->getColumnDimension($column)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);
        }

        $sheet->setCellValue($this->getColumnWord($colIndex++) . '2', $totalAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '2', $totalContractAmount != 0 ? $totalAmount / $totalContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '2', $totalContractAmount - $totalAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '2', $totalPaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '2', $totalContractAmount != 0 ? $totalPaidAmount / $totalContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '2', $totalLeftPaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex) . '2', $totalContractAmount != 0 ? $totalLeftPaidAmount / $totalContractAmount : 0);

        $sheet->setCellValue('B3', $totalMaterialContractAmount);
        $sheet->setCellValue('C3', $totalContractAmount != 0 ? $totalMaterialContractAmount / $totalContractAmount : 0);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $this->getColumnWord($colIndex++);
            $sheet->setCellValue($column . '3', $act->amount);
        }

        $sheet->setCellValue($this->getColumnWord($colIndex++) . '3', $totalMaterialAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '3', $totalMaterialContractAmount != 0 ? $totalMaterialAmount / $totalMaterialContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '3', $totalMaterialContractAmount - $totalMaterialAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '3', $totalMaterialPaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '3', $totalMaterialContractAmount != 0 ? $totalMaterialPaidAmount / $totalMaterialContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '3', $totalMaterialLeftPaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex) . '3', $totalMaterialContractAmount != 0 ? $totalMaterialLeftPaidAmount / $totalMaterialContractAmount : 0);

        $sheet->setCellValue('B4', $totalRadContractAmount);
        $sheet->setCellValue('C4', $totalContractAmount != 0 ? $totalRadContractAmount / $totalContractAmount : 0);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $this->getColumnWord($colIndex++);
            $sheet->setCellValue($column . '4', $act->rad_amount);
        }

        $sheet->setCellValue($this->getColumnWord($colIndex++) . '4', $totalRadAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '4', $totalRadContractAmount != 0 ? $totalRadAmount / $totalRadContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '4', $totalRadContractAmount - $totalRadAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '4', $totalRadPaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '4', $totalRadContractAmount != 0 ? $totalRadPaidAmount / $totalRadContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '4', $totalRadLeftPaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex) . '4', $totalRadContractAmount != 0 ? $totalRadLeftPaidAmount / $totalRadContractAmount : 0);

        $sheet->setCellValue('B5', $totalOpsteContractAmount);
        $sheet->setCellValue('C5', $totalContractAmount != 0 ? $totalOpsteContractAmount / $totalContractAmount : 0);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $this->getColumnWord($colIndex++);
            $sheet->setCellValue($column . '5', $act->opste_amount);
        }

        $sheet->setCellValue($this->getColumnWord($colIndex++) . '5', $totalOpsteAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '5', $totalOpsteContractAmount != 0 ? $totalOpsteAmount / $totalOpsteContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '5', $totalOpsteContractAmount - $totalOpsteAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '5', $totalOpstePaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '5', $totalOpsteContractAmount != 0 ? $totalOpstePaidAmount / $totalOpsteContractAmount : 0);
        $sheet->setCellValue($this->getColumnWord($colIndex++) . '5', $totalOpsteLeftPaidAmount);
        $sheet->setCellValue($this->getColumnWord($colIndex) . '5', $totalOpsteContractAmount != 0 ? $totalOpsteLeftPaidAmount / $totalOpsteContractAmount : 0);

        $sheet->getStyle('B2:B5')->getNumberFormat()->setFormatCode('#,##0');

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $this->getColumnWord($colIndex++);
            $sheet->getStyle($column . '2:' . $column . '5')->getNumberFormat()->setFormatCode('#,##0');
        }

        $sheet->getStyle($this->getColumnWord($colIndex) . '2:' . $this->getColumnWord($colIndex) . '5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($this->getColumnWord($colIndex + 2) . '2:' . $this->getColumnWord($colIndex + 2) . '5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($this->getColumnWord($colIndex + 3) . '2:' . $this->getColumnWord($colIndex + 3) . '5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($this->getColumnWord($colIndex + 5) . '2:' . $this->getColumnWord($colIndex + 5) . '5')->getNumberFormat()->setFormatCode('#,##0');


        $sheet->getStyle('C2:C5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle($this->getColumnWord($colIndex + 1) . '2:' . $this->getColumnWord($colIndex + 1) . '5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle($this->getColumnWord($colIndex + 4) . '2:' . $this->getColumnWord($colIndex + 4) . '5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle($this->getColumnWord($colIndex + 6) . '2:' . $this->getColumnWord($colIndex + 6) . '5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $sheet->getStyle('A1:' . $lastColumn . '5')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'b7b7b7']]]
        ]);
        $sheet->getStyle('B2:' . $lastColumn . '5')->getAlignment()->setVertical('center')->setHorizontal('center');
        $sheet->getStyle('A2:A5')->getAlignment()->setVertical('center');

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, $lastColumnIndex, 5);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
