<?php

namespace App\Exports\Finance\FinanceReport\Sheets;

use App\Models\CRM\ItrSalary;
use App\Models\CRM\SalaryDebt;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReport;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\Contract\ContractService;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObjectPivotSheet implements
    WithTitle,
    WithStyles,
    WithEvents
{
    private string $sheetName;

    private array $pivotInfo;

    private string $year;

    public function __construct(string $sheetName, array $pivotInfo, string $year)
    {
        $this->sheetName = $sheetName;
        $this->pivotInfo = $pivotInfo;
        $this->year = $year;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Сводная по объектам
        $this->fillObjects($sheet);
    }

    private function fillObjects(&$sheet): void
    {
        $year = $this->year;
        $infos = $this->pivotInfo['pivot_list'];
        $objects = $this->pivotInfo['objects'];
        $summary = $this->pivotInfo['pivot_info']['summary'];
        $total = $this->pivotInfo['pivot_info']['total'];

        $specialFields = ['balance_with_general_balance', 'objectBalance', 'prognozBalance'];
        $prognozFields = array_merge(array_values(FinanceReport::getPrognozFields()), ['receive_customer', 'receive_other', 'receive_retro_dtg']);
        $percentField = 'general_balance_to_receive_percentage';
        $percentFields = ['time_percent', 'complete_percent', 'money_percent', 'plan_ready_percent', 'fact_ready_percent', 'deviation_plan_percent'];
        $exceptFields = ['pay_cash', 'pay_non_cash', 'transfer_service'];

        $lastRow = count($infos) + 1 - count($exceptFields);
        $lastColumnIndex = 2 + count($objects);
        $lastColumn = $this->getColumnWord($lastColumnIndex);

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(23);
        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:A' . $lastRow)->getFont()->setSize(14);
        $sheet->getStyle('B1:B' . $lastRow)->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('B1:B' . $lastRow)->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('B2:' . $lastColumn . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);

        $sheet->getStyle('B1:B' . $lastRow)->getFill()->applyFromArray(['fillType' => 'solid', 'color' => ['rgb' => 'f7f7f7'],]);
        $sheet->getStyle('C1:' . $lastColumn . '1')->getFill()->applyFromArray(['fillType' => 'solid', 'color' => ['rgb' => 'f15a22'],]);
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('B1:B' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['bordedrStyle' => Border::BORDER_DASHED, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('C1:' . $lastColumn . '1')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'ffffff']]]
        ]);
        $sheet->getStyle('C1:' . $lastColumn . '1')->getFont()->setColor(new Color(Color::COLOR_WHITE));

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, $lastColumnIndex, 33);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);

        $sheet->setCellValue('A1', 'Сводка');
        $sheet->setCellValue('B1', 'Итого');

        $columnIndex = 3;
        foreach($objects as $object) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $object->code . ' | '  . $object->name);
            $sheet->getColumnDimension($column)->setWidth(25);
            $columnIndex++;
        }

        $row = 2;
        foreach($infos as $info => $field) {
            if (in_array($field, $exceptFields)) {
                continue;
            }
            $sumValue = $summary->{$year}->{$field};
            $isSpecialField = in_array($field, $specialFields);
            $isPrognozField = in_array($field, $prognozFields);

            $sheet->setCellValue('A' . $row, $info);

            if ($field === 'prognoz_general') {
                $sheet->setCellValue('A' . $row, 'Общие расходы (' . number_format(abs($summary->{$year}->{'general_balance_to_receive_percentage'}), 2) . '%)');
            }

            $sheet->setCellValue('B' . $row, !is_valid_amount_in_range($sumValue) ? '-' : $sumValue);

            if ($isSpecialField) {
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
                $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($sumValue < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if ($percentField === $field || in_array($field, $percentFields)) {
                $sheet->setCellValue('B' . $row, !is_valid_amount_in_range($sumValue) ? '-' : $sumValue / 100);
                $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            }

            if ($isPrognozField) {
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(false);
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setItalic(true);
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setSize(11);
            }

            if ($field === 'complete_percent' || $field === 'money_percent') {
                $sheet->getStyle('B' . $row)->getFont()->setSize(12);
                $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
            }

            $columnIndex = 3;
            foreach($objects as $object) {
                if ($percentField === $field) continue;

                $value = $total->{$year}->{$object->code}->{$field};
                $column = $this->getColumnWord($columnIndex);
                $sheet->getStyle($column . $row)->getAlignment()->setVertical('center')->setHorizontal('right');

                if (in_array($field, $percentFields)) {
                    if ($field === 'time_percent') {
                        $sheet->setCellValue($column . $row, $value === 0 ? 'Нет данных' : $value / 100);
                    } else if ($field === 'deviation_plan_percent') {
                        if ($value != 0) {
                            $sheet->getStyle($column . $row)->getFont()->setColor(new Color($value < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                        }
                        $sheet->setCellValue($column . $row, !is_valid_amount_in_range($value) ? '-' : $value / 100);
                    } else {
                        $sheet->setCellValue($column . $row, !is_valid_amount_in_range($value) ? '-' : $value / 100);
                    }
                    $sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                } else {
                    $sheet->setCellValue($column . $row, !is_valid_amount_in_range($value) ? '-' : $value);
                }

                if ($isSpecialField) {
                    $sheet->getStyle($column . $row)->getFont()->setSize(12);
                    $sheet->getStyle($column . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
                    $sheet->getStyle($column . $row)->getFont()->setColor(new Color($value < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                if ($isPrognozField) {
                    $sheet->getStyle($column . $row)->getFont()->setSize(11);
                    $sheet->getStyle($column . $row)->getFont()->setItalic(true);
                }

                if ($field === 'prognoz_total') {
                    $sheet->getStyle($column . $row)->getFont()->setSize(12);
                    $sheet->getStyle($column . $row)->getFont()->setBold(true);
                    $sheet->getStyle($column . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
                }

                if ($field === 'complete_percent' || $field === 'money_percent') {
                    $sheet->getStyle($column . $row)->getFont()->setSize(12);
//                    $sheet->getStyle($column . $row)->getFont()->setBold(true);
                    $sheet->getStyle($column . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
                }

                $columnIndex++;
            }

            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;
        }
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->freezePane('C2');
            },
        ];
    }
}
