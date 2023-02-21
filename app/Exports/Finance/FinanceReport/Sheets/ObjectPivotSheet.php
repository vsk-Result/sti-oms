<?php

namespace App\Exports\Finance\FinanceReport\Sheets;

use App\Models\CRM\ItrSalary;
use App\Models\CRM\SalaryDebt;
use App\Models\CurrencyExchangeRate;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObjectPivotSheet implements
    WithTitle,
    WithStyles,
    WithEvents
{
    private string $sheetName;

    private array $pivotInfo;

    public function __construct(string $sheetName, array $pivotInfo)
    {
        $this->sheetName = $sheetName;
        $this->pivotInfo = $pivotInfo;
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
        $infos = $this->pivotInfo['pivot_list'];
        $objects = $this->pivotInfo['objects'];
        $summary = $this->pivotInfo['pivot_info']['summary'];
        $total = $this->pivotInfo['pivot_info']['total'];

        $row = 1;
        $rowTitle = $row;

        $sheet->setCellValue('A' . $rowTitle, 'Сводка');
        $sheet->setCellValue('B' . $rowTitle, 'Итого');

        $columnIndex = 3;
        foreach($objects as $object) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowTitle, $object->getName());
            $sheet->getColumnDimension($column)->setWidth(35);
            $columnIndex++;
        }

        $row++;
        foreach($infos as $info => $field) {
            $sheet->setCellValue('A' . $row, $info);

            if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24') {
                continue;
            }

            if (in_array($field, ['payment_total_balance', 'general_costs_amount'])) {
                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB'));
            } elseif ($field === 'contractor' || $field === 'provider') {
                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB', 0, true));
            } elseif ($field === 'salary_itr' || $field === 'salary_work') {
                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB', 0, true));
            } else {
                $sheet->setCellValue('B' . $row, CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($summary[$field]['EUR'], 'EUR', 0, true));
            }

            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($summary[$field]['RUB'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex = 3;
            foreach($objects as $object) {
                $column = $this->getColumnWord($columnIndex);
                if (in_array($field, ['payment_total_balance', 'general_costs_amount', 'general_costs_amount_1', 'general_costs_amount_24'])) {
                    if ($object->code == 288) {
                        if ($field === 'general_costs_amount') {
                            $sheet->setCellValue($column . $row, CurrencyExchangeRate::format($total[$object->code]['general_costs_amount_1'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($total[$object->code]['general_costs_amount_24'], 'RUB', 0, true));
                            $sheet->getStyle($column . $row)->getFont()->setColor(new Color($total[$object->code]['general_costs_amount_1'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

                            $columnIndex++;
                            continue;
                        }
                        if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24') {
                            continue;
                        } else {
                            $sheet->setCellValue($column . $row, CurrencyExchangeRate::format($total[$object->code][$field], 'RUB', 0, true));
                            $sheet->getStyle($column . $row)->getFont()->setColor(new Color($total[$object->code][$field] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                        }
                    } else {
                        if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24') {
                            continue;
                        }
                        $sheet->setCellValue($column . $row, CurrencyExchangeRate::format($total[$object->code][$field], 'RUB', 0, true));
                        $sheet->getStyle($column . $row)->getFont()->setColor(new Color($total[$object->code][$field] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    }
                } else if (in_array($field, ['contractor', 'provider', 'salary_itr', 'salary_work'])) {
                    $sheet->setCellValue($column . $row, CurrencyExchangeRate::format($total[$object->code][$field]['RUB'], 'RUB', 0, true));
                    $sheet->getStyle($column . $row)->getFont()->setColor(new Color($total[$object->code][$field]['RUB'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                } else {
                    $sheet->setCellValue($column . $row, CurrencyExchangeRate::format($total[$object->code][$field]['RUB'], 'RUB', 0, true) . "\n" . CurrencyExchangeRate::format($total[$object->code][$field]['EUR'], 'RUB', 0, true));
                    $sheet->getStyle($column . $row)->getFont()->setColor(new Color($total[$object->code][$field]['RUB'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                $columnIndex++;
            }

            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;
        }

        $row--;

        $lastColumn = $this->getColumnWord(2 + count($objects));

        $sheet->getRowDimension($rowTitle)->setRowHeight(50);
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getStyle('A' . $rowTitle . ':' . $lastColumn . $rowTitle)->getFont()->setBold(true);
        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowTitle . ':' . $lastColumn . $rowTitle)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A' . ($rowTitle + 1) . ':A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B' . ($rowTitle + 1) . ':' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(true);

        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('C' . $rowTitle . ':' . $lastColumn . $rowTitle)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f15a22');
        $sheet->getStyle('A' . $rowTitle . ':' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);
        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_DASHED, 'color' => ['rgb' => 'f15a22']]]
        ]);
        $sheet->getStyle('C' . $rowTitle . ':' . $lastColumn . $rowTitle)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'ffffff']]]
        ]);
        $sheet->getStyle('C' . $rowTitle . ':' . $lastColumn . $rowTitle)->getFont()->setColor(new Color(Color::COLOR_WHITE));
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
