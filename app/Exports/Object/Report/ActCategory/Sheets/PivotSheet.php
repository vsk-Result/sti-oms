<?php

namespace App\Exports\Object\Report\ActCategory\Sheets;

use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\Contract\ActService;
use App\Services\CurrencyExchangeRateService;
use Carbon\Carbon;
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
    private CurrencyExchangeRateService $currencyExchangeService;

    public function __construct(ActService $actService, BObject $object, CurrencyExchangeRateService $currencyExchangeService)
    {
        $this->actService = $actService;
        $this->object = $object;
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function title(): string
    {
        return 'Отчет';
    }

    public function styles(Worksheet $sheet): void
    {
        $object = $this->object;
        $total = [];
        $acts = $this->actService->filterActs(['object_id' => [$object->id], 'currency' => 'RUB'], $total, false);
        $actsEUR = $this->actService->filterActs(['object_id' => [$object->id], 'currency' => 'EUR'], $total, false);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getRowDimension(3)->setRowHeight(30);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(30);

        $lastColumnIndex = $acts->count() + $actsEUR->count() + 10;
        $lastColumn = $sheet->getColumnDimensionByColumn($lastColumnIndex)->getColumnIndex();

        for ($i = 1; $i <= $lastColumnIndex; $i++) {
            $column = $sheet->getColumnDimensionByColumn($i)->getColumnIndex();
            $sheet->getColumnDimension($column)->setWidth(30);
        }

        $sheet->getStyle('A1:' . $lastColumn . '2')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->setCellValue('A1', 'Категория');
        $sheet->setCellValue('B1', 'Сумма по договору');
        $sheet->setCellValue('C1', '% по договору');

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '1', 'Акт ' . $act->number  . ' (RUB)');
        }

        foreach ($actsEUR as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '1', 'Акт ' . $act->number  . ' (EUR)');
        }

        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '1', 'Итого выполнение');
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '1', '% выполнение');
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '1', 'Остаток к выполнению');
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '1', 'Оплата');
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '1', '% оплаты');
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '1', 'Остаток к оплате');
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex)->getColumnIndex() . '1', '% остатка к оплате');

        $sheet->getStyle('A3:A5')->getFont()->setItalic(true);
        $sheet->getStyle('A2:' . $lastColumn . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->setCellValue( 'A2', $object->getName());
        $sheet->setCellValue( 'A3', 'Материалы');
        $sheet->setCellValue( 'A4', 'Работы');
        $sheet->setCellValue( 'A5', 'Накладные');

        $EURExchangeRate = $this->currencyExchangeService->getExchangeRate(Carbon::now()->format('Y-m-d'), 'EUR');

        $totalMaterialAmount = $acts->sum('amount');
        $totalRadAmount = $acts->sum('rad_amount');
        $totalOpsteAmount = $acts->sum('opste_amount');

        if ($EURExchangeRate) {
            $totalMaterialAmount += $actsEUR->sum('amount') * $EURExchangeRate->rate;
            $totalRadAmount += $actsEUR->sum('rad_amount') * $EURExchangeRate->rate;
            $totalOpsteAmount += $actsEUR->sum('opste_amount') * $EURExchangeRate->rate;
        }

        $totalContractAmount = 0;
        $totalMaterialContractAmount = 0;
        $totalRadContractAmount = 0;
        $totalOpsteContractAmount = 0;

        foreach ($object->contracts()->where('type_id', Contract::TYPE_MAIN)->get() as $contract) {
            $totalContractAmount += $contract->getAmount('RUB');
            $totalMaterialContractAmount += $contract->getMaterialAmount('RUB');
            $totalRadContractAmount += $contract->getRadAmount('RUB');
            $totalOpsteContractAmount += $contract->getOpsteAmount('RUB');

            if ($EURExchangeRate) {
                $totalContractAmount += $contract->getAmount('EUR') * $EURExchangeRate->rate;
                $totalMaterialContractAmount += $contract->getMaterialAmount('EUR') * $EURExchangeRate->rate;
                $totalRadContractAmount += $contract->getRadAmount('EUR') * $EURExchangeRate->rate;
                $totalOpsteContractAmount += $contract->getOpsteAmount('EUR') * $EURExchangeRate->rate;
            }
        }

        $receivePaymentsRUB = $object->payments()
            ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
            ->where('company_id', 1)
            ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
            ->where('currency', 'RUB')
            ->get();

        $totalMaterialPaidAmount = $receivePaymentsRUB->where('category', Payment::CATEGORY_MATERIAL)->sum('amount');
        $totalRadPaidAmount = $receivePaymentsRUB->where('category', Payment::CATEGORY_RAD)->sum('amount');
        $totalOpstePaidAmount = $receivePaymentsRUB->where('category', Payment::CATEGORY_OPSTE)->sum('amount');

        if ($EURExchangeRate) {
            $receivePaymentsEUR = $object->payments()
                ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
                ->where('company_id', 1)
                ->whereIn('organization_sender_id', $object->customers->pluck('id')->toArray())
                ->where('currency', 'EUR')
                ->get();

            $totalMaterialPaidAmount += $receivePaymentsEUR->where('category', Payment::CATEGORY_MATERIAL)->sum('currency_amount') * $EURExchangeRate->rate;
            $totalRadPaidAmount += $receivePaymentsEUR->where('category', Payment::CATEGORY_RAD)->sum('currency_amount') * $EURExchangeRate->rate;
            $totalOpstePaidAmount += $receivePaymentsEUR->where('category', Payment::CATEGORY_OPSTE)->sum('currency_amount') * $EURExchangeRate->rate;
        }

        $totalMaterialLeftPaidAmount = $totalMaterialContractAmount - $totalMaterialPaidAmount;
        $totalRadLeftPaidAmount = $totalRadContractAmount - $totalRadPaidAmount;
        $totalOpsteLeftPaidAmount = $totalOpsteContractAmount - $totalOpstePaidAmount;

        $totalAmount = $totalMaterialAmount + $totalRadAmount + $totalOpsteAmount;
        $totalPaidAmount = $totalMaterialPaidAmount + $totalRadPaidAmount + $totalOpstePaidAmount;
        $totalLeftPaidAmount = $totalMaterialLeftPaidAmount + $totalRadLeftPaidAmount + $totalOpsteLeftPaidAmount;

        $totalPaidAmountPercent = $totalContractAmount != 0 ? $totalPaidAmount / $totalContractAmount : 0;
        $totalMaterialPaidAmountPercent = $totalMaterialContractAmount != 0 ? $totalMaterialPaidAmount / $totalMaterialContractAmount : 0;
        $totalRadPaidAmountPercent = $totalRadContractAmount != 0 ? $totalRadPaidAmount / $totalRadContractAmount : 0;
        $totalOpstePaidAmountPercent = $totalOpsteContractAmount != 0 ? $totalOpstePaidAmount / $totalOpsteContractAmount : 0;

        $totalAmountPercent = $totalContractAmount != 0 ? $totalAmount / $totalContractAmount : 0;
        $totalMaterialAmountPercent = $totalMaterialContractAmount != 0 ? $totalMaterialAmount / $totalMaterialContractAmount : 0;
        $totalRadAmountPercent = $totalRadContractAmount != 0 ? $totalRadAmount / $totalRadContractAmount : 0;
        $totalOpsteAmountPercent = $totalOpsteContractAmount != 0 ? $totalOpsteAmount / $totalOpsteContractAmount : 0;


        $sheet->setCellValue('B2', $totalContractAmount);
        $sheet->setCellValue('C2', 1);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '2', $act->getAmount());
            $sheet->getColumnDimension($column)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);
        }
        foreach ($actsEUR as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '2', $act->getAmount());
            $sheet->getColumnDimension($column)->setOutlineLevel(1)
                ->setVisible(false)
                ->setCollapsed(true);
        }

        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '2', $totalAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '2', $totalAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '2', $totalContractAmount - $totalAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '2', $totalPaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '2', $totalPaidAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '2', $totalLeftPaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex)->getColumnIndex() . '2', $totalContractAmount != 0 ? $totalLeftPaidAmount / $totalContractAmount : 0);

        $sheet->setCellValue('B3', $totalMaterialContractAmount);
        $sheet->setCellValue('C3', $totalContractAmount != 0 ? $totalMaterialContractAmount / $totalContractAmount : 0);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '3', $act->amount);
        }
        foreach ($actsEUR as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '3', $act->amount);
        }

        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '3', $totalMaterialAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '3', $totalMaterialAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '3', $totalMaterialContractAmount - $totalMaterialAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '3', $totalMaterialPaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '3', $totalMaterialPaidAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '3', $totalMaterialLeftPaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex)->getColumnIndex() . '3', $totalMaterialContractAmount != 0 ? $totalMaterialLeftPaidAmount / $totalMaterialContractAmount : 0);

        $sheet->setCellValue('B4', $totalRadContractAmount);
        $sheet->setCellValue('C4', $totalContractAmount != 0 ? $totalRadContractAmount / $totalContractAmount : 0);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '4', $act->rad_amount);
        }
        foreach ($actsEUR as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '4', $act->rad_amount);
        }

        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '4', $totalRadAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '4', $totalRadAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '4', $totalRadContractAmount - $totalRadAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '4', $totalRadPaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '4', $totalRadPaidAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '4', $totalRadLeftPaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex)->getColumnIndex() . '4', $totalRadContractAmount != 0 ? $totalRadLeftPaidAmount / $totalRadContractAmount : 0);

        $sheet->setCellValue('B5', $totalOpsteContractAmount);
        $sheet->setCellValue('C5', $totalContractAmount != 0 ? $totalOpsteContractAmount / $totalContractAmount : 0);

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '5', $act->opste_amount);
        }
        foreach ($actsEUR as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->setCellValue($column . '5', $act->opste_amount);
        }

        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '5', $totalOpsteAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '5', $totalOpsteAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '5', $totalOpsteContractAmount - $totalOpsteAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '5', $totalOpstePaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '5', $totalOpstePaidAmountPercent);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex() . '5', $totalOpsteLeftPaidAmount);
        $sheet->setCellValue($sheet->getColumnDimensionByColumn($colIndex)->getColumnIndex() . '5', $totalOpsteContractAmount != 0 ? $totalOpsteLeftPaidAmount / $totalOpsteContractAmount : 0);

        $sheet->getStyle('B2:B5')->getNumberFormat()->setFormatCode('#,##0');

        $colIndex = 4;
        foreach ($acts as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->getStyle($column . '2:' . $column . '5')->getNumberFormat()->setFormatCode('#,##0');
        }
        foreach ($actsEUR as $act) {
            $column = $sheet->getColumnDimensionByColumn($colIndex++)->getColumnIndex();
            $sheet->getStyle($column . '2:' . $column . '5')->getNumberFormat()->setFormatCode('#,##0');
        }

        $sheet->getStyle($sheet->getColumnDimensionByColumn($colIndex)->getColumnIndex() . '2:' . $sheet->getColumnDimensionByColumn($colIndex)->getColumnIndex() . '5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($sheet->getColumnDimensionByColumn($colIndex + 2)->getColumnIndex() . '2:' . $sheet->getColumnDimensionByColumn($colIndex + 2)->getColumnIndex() . '5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($sheet->getColumnDimensionByColumn($colIndex + 3)->getColumnIndex() . '2:' . $sheet->getColumnDimensionByColumn($colIndex + 3)->getColumnIndex() . '5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle($sheet->getColumnDimensionByColumn($colIndex + 5)->getColumnIndex() . '2:' . $sheet->getColumnDimensionByColumn($colIndex + 5)->getColumnIndex() . '5')->getNumberFormat()->setFormatCode('#,##0');


        $sheet->getStyle('C2:C5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle($sheet->getColumnDimensionByColumn($colIndex + 1)->getColumnIndex() . '2:' . $sheet->getColumnDimensionByColumn($colIndex + 1)->getColumnIndex() . '5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle($sheet->getColumnDimensionByColumn($colIndex + 4)->getColumnIndex() . '2:' . $sheet->getColumnDimensionByColumn($colIndex + 4)->getColumnIndex() . '5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle($sheet->getColumnDimensionByColumn($colIndex + 6)->getColumnIndex() . '2:' . $sheet->getColumnDimensionByColumn($colIndex + 6)->getColumnIndex() . '5')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

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
}
