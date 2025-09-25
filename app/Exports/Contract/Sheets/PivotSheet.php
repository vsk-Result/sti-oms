<?php

namespace App\Exports\Contract\Sheets;

use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private BObject $object;
    private Collection $contracts;
    private array $total;

    public function __construct(BObject $object, Collection $contracts, array $total)
    {
        $this->object = $object;
        $this->contracts = $contracts;
        $this->total = $total;
    }

    public function title(): string
    {
        return 'Договора';
    }

    public function styles(Worksheet $sheet): void
    {
        $total = $this->total;
        $THINStyleArray = [ 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '0000000'],],],];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'Номер');
        $sheet->setCellValue('B1', 'Валюта');
        $sheet->setCellValue('C1', 'Сумма');
        $sheet->setCellValue('D1', 'Сумма аванса');
        $sheet->setCellValue('E1', 'Сумма получ. аванса');
        $sheet->setCellValue('F1', 'Сумма аванса к получению');
        $sheet->setCellValue('G1', 'Выполнено по актам');
        $sheet->setCellValue('H1', 'Аванс удержан по актам');
        $sheet->setCellValue('I1', 'Депозит удержан по актам');
        $sheet->setCellValue('J1', 'К оплате по актам');
        $sheet->setCellValue('K1', 'Оплачено по актам');
        $sheet->setCellValue('L1', 'Сумма неоплаченных работ по актам');
        $sheet->setCellValue('M1', 'Остаток неотработанного аванса');

        $sheet->getColumnDimension('A')->setWidth(60);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(25);
        $sheet->getColumnDimension('L')->setWidth(25);
        $sheet->getColumnDimension('M')->setWidth(25);

        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $sheet->getRowDimension(1)->setRowHeight(45);

        $row = 2;
        $currencies = ['RUB', 'EUR'];
        foreach ($currencies as $currency) {
            foreach ($this->contracts as $contract) {
                if ($contract->currency !== $currency) {
                    continue;
                }

                $sheet->setCellValue('A' . $row, $contract->getName());
                $sheet->setCellValue('B' . $row, $contract->currency);
                $sheet->setCellValue('C' . $row, $this->formatAmount($contract->getAmount($currency)));
                $sheet->setCellValue('D' . $row, $this->formatAmount($contract->getAvansesAmount($currency)));
                $sheet->setCellValue('E' . $row, $this->formatAmount($contract->getAvansesReceivedAmount($currency)));
                $sheet->setCellValue('F' . $row, $this->formatAmount($contract->getAvansesLeftAmount($currency)));
                $sheet->setCellValue('G' . $row, $this->formatAmount($contract->getActsAmount($currency)));
                $sheet->setCellValue('H' . $row, $this->formatAmount($contract->getActsAvasesAmount($currency)));
                $sheet->setCellValue('I' . $row, $this->formatAmount($contract->getActsDepositesAmount($currency)));
                $sheet->setCellValue('J' . $row, $this->formatAmount($contract->getActsNeedPaidAmount($currency)));
                $sheet->setCellValue('K' . $row, $this->formatAmount($contract->getActsPaidAmount($currency)));
                $sheet->setCellValue('L' . $row, $this->formatAmount($contract->getActsLeftPaidAmount($currency)));
                $sheet->setCellValue('M' . $row, $this->formatAmount($contract->getNotworkLeftAmount($currency)));

                $sheet->getRowDimension($row)->setRowHeight(40);
                $row++;

                $subContracts = $contract->children->where('currency', $currency);

                if ($subContracts->count() > 0) {
                    $mainContract = $contract;
                    $mainContract->type_id = Contract::TYPE_ADDITIONAL;

                    $sheet->setCellValue('A' . $row, '        ' . 'Основной договор');
                    $sheet->setCellValue('B' . $row, $mainContract->currency);
                    $sheet->setCellValue('C' . $row, $this->formatAmount($mainContract->getAmount($currency)));
                    $sheet->setCellValue('D' . $row, $this->formatAmount($mainContract->getAvansesAmount($currency)));
                    $sheet->setCellValue('E' . $row, $this->formatAmount($mainContract->getAvansesReceivedAmount($currency)));
                    $sheet->setCellValue('F' . $row, $this->formatAmount($mainContract->getAvansesLeftAmount($currency)));
                    $sheet->setCellValue('G' . $row, $this->formatAmount($mainContract->getActsAmount($currency)));
                    $sheet->setCellValue('H' . $row, $this->formatAmount($mainContract->getActsAvasesAmount($currency)));
                    $sheet->setCellValue('I' . $row, $this->formatAmount($mainContract->getActsDepositesAmount($currency)));
                    $sheet->setCellValue('J' . $row, $this->formatAmount($mainContract->getActsNeedPaidAmount($currency)));
                    $sheet->setCellValue('K' . $row, $this->formatAmount($mainContract->getActsPaidAmount($currency)));
                    $sheet->setCellValue('L' . $row, $this->formatAmount($mainContract->getActsLeftPaidAmount($currency)));
                    $sheet->setCellValue('M' . $row, $this->formatAmount($mainContract->getNotworkLeftAmount($currency)));

                    $sheet->getRowDimension($row)->setRowHeight(25);
                    $sheet->getStyle('A' . $row . ':M' . $row)->getFont()->setItalic(true);

                    $sheet->getRowDimension($row)->setOutlineLevel(1)
                        ->setVisible(false)
                        ->setCollapsed(true);

                    $row++;
                }

                foreach ($subContracts as $subContract) {
                    $sheet->setCellValue('A' . $row, '        ' . $subContract->getName());
                    $sheet->setCellValue('B' . $row, $subContract->currency);
                    $sheet->setCellValue('C' . $row, $this->formatAmount($subContract->getAmount($currency)));
                    $sheet->setCellValue('D' . $row, $this->formatAmount($subContract->getAvansesAmount($currency)));
                    $sheet->setCellValue('E' . $row, $this->formatAmount($subContract->getAvansesReceivedAmount($currency)));
                    $sheet->setCellValue('F' . $row, $this->formatAmount($subContract->getAvansesLeftAmount($currency)));
                    $sheet->setCellValue('G' . $row, $this->formatAmount($subContract->getActsAmount($currency)));
                    $sheet->setCellValue('H' . $row, $this->formatAmount($subContract->getActsAvasesAmount($currency)));
                    $sheet->setCellValue('I' . $row, $this->formatAmount($subContract->getActsDepositesAmount($currency)));
                    $sheet->setCellValue('J' . $row, $this->formatAmount($subContract->getActsNeedPaidAmount($currency)));
                    $sheet->setCellValue('K' . $row, $this->formatAmount($subContract->getActsPaidAmount($currency)));
                    $sheet->setCellValue('L' . $row, $this->formatAmount($subContract->getActsLeftPaidAmount($currency)));
                    $sheet->setCellValue('M' . $row, $this->formatAmount($subContract->getNotworkLeftAmount($currency)));

                    $sheet->getRowDimension($row)->setRowHeight(25);
                    $sheet->getStyle('A' . $row . ':M' . $row)->getFont()->setItalic(true);

                    $sheet->getRowDimension($row)->setOutlineLevel(1)
                        ->setVisible(false)
                        ->setCollapsed(true);

                    $row++;
                }
            }
        }

        foreach ($currencies as $currency) {
            if ($this->contracts->where('currency', $currency)->count() > 0) {
                $sheet->setCellValue('A' . $row, 'Итого');
                $sheet->setCellValue('B' . $row, $currency);
                $sheet->setCellValue('C' . $row, $total['amount'][$currency]);
                $sheet->setCellValue('D' . $row, $total['avanses_amount'][$currency]);
                $sheet->setCellValue('E' . $row, $total['avanses_received_amount'][$currency]);
                $sheet->setCellValue('F' . $row, $total['avanses_left_amount'][$currency]);
                $sheet->setCellValue('G' . $row, $total['acts_amount'][$currency]);
                $sheet->setCellValue('H' . $row, $total['avanses_acts_avanses_amount'][$currency]);
                $sheet->setCellValue('I' . $row, $total['avanses_acts_deposites_amount'][$currency]);
                $sheet->setCellValue('J' . $row, $total['avanses_acts_paid_amount'][$currency] + $total['avanses_acts_left_paid_amount'][$currency]);
                $sheet->setCellValue('K' . $row, $total['avanses_acts_paid_amount'][$currency]);
                $sheet->setCellValue('L' . $row, $total['avanses_acts_left_paid_amount'][$currency]);
                $sheet->setCellValue('M' . $row, $total['avanses_notwork_left_amount'][$currency]);

                $row++;
            }
        }

        $row--;

        $sheet->getStyle('A' . $row . ':M' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':M' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:M' . $row)->applyFromArray($THINStyleArray);
        $sheet->getStyle('A1:M1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('B2:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('C2:M' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('C2:M' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getRowDimension($row)->setRowHeight(20);
    }

    public function formatAmount($amount)
    {
        if (empty($amount) || ! is_valid_amount_in_range($amount)) {
            return '-';
        }

        return $amount;
    }
}
