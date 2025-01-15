<?php

namespace App\Exports\Object\Balance\Sheets;

use App\Models\FinanceReport;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnalyticsSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    private BObject $object;

    public function __construct(BObject $object) {
        $this->object = $object;
    }

    public function title(): string
    {
        return 'Аналитика';
    }

    public function styles(Worksheet $sheet): void
    {
        $object = $this->object;
        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $infoObj = [];

        foreach ($years as $year => $objects) {
            foreach ($objects as $o) {
                if ($o->id === $object->id) {
                    $infoObj = (array) $total->{$year}->{$object->code};
                    break;
                }
            }
        }

        $specialFields = ['balance_with_general_balance', 'objectBalance', 'prognozBalance', 'planProfitability'];
        $prognozFields = array_merge(
            array_values(FinanceReport::getPrognozFields()),
            [
                'receive_customer', 'receive_other', 'receive_retro_dtg', 'transfer_service',
                'office_service', 'planProfitability_material', 'planProfitability_rad',
                'ostatokNeotrabotannogoAvansaFix', 'ostatokNeotrabotannogoAvansaFloat',
                'pay_opste', 'pay_rad', 'pay_material', 'pay_salary', 'pay_tax',
                'provider_debt_fix', 'provider_debt_float', 'general_balance_salary', 'general_balance_tax',
                'general_balance_material', 'general_balance_service'
            ]
        );
        $percentField = 'general_balance_to_receive_percentage';
        $percentFields = ['time_percent', 'complete_percent', 'money_percent', 'plan_ready_percent', 'fact_ready_percent', 'deviation_plan_percent'];
        $exceptFields = [
            'pay_cash', 'pay_non_cash', 'total_debts', 'customer_debts', 'pay_customers', 'pay_transfer', 'pay_empty', 'office_service', 'general_balance_material',
            'general_balance_salary', 'general_balance_tax', 'general_balance_material', 'general_balance_service',
            'planProfitability', 'planProfitability_material', 'planProfitability_rad', 'time_percent', 'complete_percent', 'money_percent',
            'plan_ready_percent', 'fact_ready_percent', 'deviation_plan_percent', 'prognoz_consalting_after_work'
        ];

        $infos = FinanceReport::getInfoFields();

        foreach ($infos as $field) {
            if (str_contains($field, '_without_nds')) {
                $exceptFields[] = $field;
            }
        }
        $pivotFields = ['receive', 'pay', 'balance', 'general_balance', 'balance_with_general_balance', 'total_debts', 'customer_debts', 'objectBalance', 'ostatokPoDogovoruSZakazchikom', 'prognoz_total', 'prognozBalance'];

        $thirdLevelFields = [
            'receive_customer_fix_avans', 'receive_customer_target_avans', 'receive_customer_acts', 'receive_customer_gu',
            'pay_material_fix', 'pay_material_float', 'prognoz_material_fix', 'prognoz_material_float'
        ];

        $lastRow = count($infos) + 1 - count($exceptFields);

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);
        $sheet->getStyle('A1:A' . $lastRow)->getFont()->setSize(14);
        $sheet->getStyle('B1:B' . $lastRow)->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('B1:B' . $lastRow)->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('B2:B' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A1:B' . '1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);

        $sheet->getStyle('A1:B' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('B1:B' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['bordedrStyle' => Border::BORDER_DASHED, 'color' => ['rgb' => '000000']]]
        ]);

        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, 2, 49);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);

        $sheet->setCellValue('A1', 'Сводка');
        $sheet->setCellValue('B1', $object->code);

        $row = 2;
        foreach($infos as $info => $field) {
            if (in_array($field, $exceptFields)) {
                continue;
            }

//            $sumValue = $summary->{$year}->{$field};

            if ($field === 'general_balance_service') {
//                $sumValue += $summary->{$year}->{'general_balance_material'};
            }

            $isSpecialField = in_array($field, $specialFields);
            $isPrognozField = in_array($field, $prognozFields);
            $isThirdLevelField = in_array($field, $thirdLevelFields);

            if ($isPrognozField) {
                $info = str_repeat(' ', 8) . $info;
            }

            if ($isThirdLevelField) {
                $info = str_repeat(' ', 16) . $info;
            }

            $sheet->setCellValue('A' . $row, $info);

            if ($field === 'prognoz_general') {
                $sheet->setCellValue('A' . $row, 'Общие расходы%)');
                $sheet->setCellValue('A' . $row, 'Общие расходы (' . number_format(abs($infoObj['general_balance_to_receive_percentage']), 2) . '%)');
            }

            if ($field === 'pay_tax') {
                $sheet->setCellValue('A' . $row, $info . ' (% налог/ з/п)');
                $sheet->setCellValue('A' . $row, $info . ' (' . number_format(abs($infoObj['pay_salary'] != 0 ? ($infoObj['pay_tax'] /$infoObj['pay_salary'] * 100) : 0), 2) . '% налог/ з/п)');
            }

            if ($field === 'general_balance_service') {
                $sheet->setCellValue('A' . $row, 'Расходы на услуги, материалы');
            }

//            $sheet->setCellValue('B' . $row, !is_valid_amount_in_range($sumValue) ? '-' : $sumValue);

            if ($isSpecialField) {
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->getStyle('A' . $row . ':B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
//                $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($sumValue < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }

            if ($percentField === $field || in_array($field, $percentFields)) {
//                $sheet->setCellValue('B' . $row, !is_valid_amount_in_range($sumValue) ? '-' : $sumValue / 100);
//                $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
            }

            if ($isPrognozField) {
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(false);
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setItalic(true);
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setSize(11);
            }

            if ($isThirdLevelField) {
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setBold(false);
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setItalic(true);
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setSize(10);
            }

            if ($field === 'complete_percent' || $field === 'money_percent') {
                $sheet->getStyle('B' . $row)->getFont()->setSize(12);
                $sheet->getStyle('B' . $row)->getFont()->setBold(true);
                $sheet->getStyle('B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center');
            }

            if ($percentField === $field) continue;

            $value = $infoObj[$field];

            $column = 'B';
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

            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;
        }
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
