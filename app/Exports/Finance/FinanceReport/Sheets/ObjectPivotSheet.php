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
        $row = 1;
        $rowTitle = $row;

        $sheet->setCellValue('A' . $rowTitle, 'Сводка');
        $sheet->setCellValue('B' . $rowTitle, 'Итого');

        $columnIndex = 3;
        foreach($objects as $object) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $rowTitle, $object->code . ' | '  . $object->name);
            $sheet->getColumnDimension($column)->setWidth(17);
            $columnIndex++;
        }

        $row++;
        foreach($infos as $info => $field) {
            if ($row === 6) {
                $sheet->setCellValue('A' . $row, 'Общие расходы / приходы %');
                $sheet->setCellValue('B' . $row, number_format($summary->{$year}->{'general_balance'} / $summary->{$year}->{'receive'} * -100, 2, ',', ' ') . '%');
                $sheet->getRowDimension($row)->setRowHeight(50);
                $row++;
            }
            $sheet->setCellValue('A' . $row, $info);

            $sheet->setCellValue('B' . $row, $summary->{$year}->{$field});
            if ($row === 7 || $row === 16 || $row === 20) {
                $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($summary->{$year}->{$field} < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            }
            $columnIndex = 3;
            foreach($objects as $object) {
                $column = $this->getColumnWord($columnIndex);
                $sheet->setCellValue($column . $row, $total->{$year}->{$object->code}->{$field} === 0 ? '-' : $total->{$year}->{$object->code}->{$field});
                if ($total->{$year}->{$object->code}->{$field} !== 0) {
                    $sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                }
                if ($row === 7 || $row === 16 ||$row === 20) {
                    $sheet->getStyle($column . $row)->getFont()->setColor(new Color($total->{$year}->{$object->code}->{$field} < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
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
        $sheet->getColumnDimension('B')->setWidth(23);
        $sheet->getStyle('A' . $rowTitle . ':' . $lastColumn . $rowTitle)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowTitle . ':A' . $row)->getFont()->setSize(14);
        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('A' . $rowTitle . ':' . $lastColumn . $rowTitle)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A' . ($rowTitle + 1) . ':A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('B' . ($rowTitle + 1) . ':' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(true);
        $sheet->getStyle('A7:' . $lastColumn . '7')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A16:' . $lastColumn . '16')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A20:' . $lastColumn . '20')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);

        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('C' . $rowTitle . ':' . $lastColumn . $rowTitle)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f15a22');
        $sheet->getStyle('A' . $rowTitle . ':' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['bordedrStyle' => Border::BORDER_DASHED, 'color' => ['rgb' => '000000']]]
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
