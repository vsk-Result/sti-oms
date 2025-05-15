<?php

namespace App\Exports\Pivot\CalculateWorkersCost\Sheets;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private array $info;

    public function __construct(string $sheetName, array $info)
    {
        $this->sheetName = $sheetName;
        $this->info = $info;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $year = 2024;
        $quarts = [
            '1 квартал' => [$year . '-01-01', $year . '-03-31'],
            '2 квартал' => [$year . '-04-01', $year . '-06-30'],
            '3 квартал' => [$year . '-07-01', $year . '-09-30'],
            '4 квартал' => [$year . '-10-01', $year . '-12-31'],
        ];

        $sheet->setCellValue('A1', 'Раздел');
        $sheet->setCellValue('B1', $year);
        $sheet->setCellValue('B2', '1 квартал');
        $sheet->setCellValue('C2', 'Расчет по часу');
        $sheet->setCellValue('D2', '2 квартал');
        $sheet->setCellValue('E2', 'Расчет по часу');
        $sheet->setCellValue('F2', '3 квартал');
        $sheet->setCellValue('G2', 'Расчет по часу');
        $sheet->setCellValue('H2', '4 квартал');
        $sheet->setCellValue('I2', 'Расчет по часу');

        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:I1');

        $rowIndex = 3;
        foreach ($this->info['data'] as $items) {
            foreach($items as $item) {
                if (str_starts_with($item['group'], '- ')) {
                    $sheet->setCellValue('A' . $rowIndex, '    ' . $item['group']);
                } else {
                    $sheet->setCellValue('A' . $rowIndex, $item['group']);
                }

                $sheet->setCellValue('B' . $rowIndex, $item['quarts']['1 квартал']['amount']);
                $sheet->setCellValue('C' . $rowIndex, $item['quarts']['1 квартал']['rate']);

                $sheet->setCellValue('D' . $rowIndex, $item['quarts']['2 квартал']['amount']);
                $sheet->setCellValue('E' . $rowIndex, $item['quarts']['2 квартал']['rate']);

                $sheet->setCellValue('F' . $rowIndex, $item['quarts']['3 квартал']['amount']);
                $sheet->setCellValue('G' . $rowIndex, $item['quarts']['3 квартал']['rate']);

                $sheet->setCellValue('H' . $rowIndex, $item['quarts']['4 квартал']['amount']);
                $sheet->setCellValue('I' . $rowIndex, $item['quarts']['4 квартал']['rate']);

                $sheet->getRowDimension($rowIndex)->setRowHeight(30);
                $rowIndex++;
            }
        }

        $sheet->setCellValue('A' . $rowIndex, 'Итого');
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);

        $sheet->setCellValue('B' . $rowIndex, $this->info['total']['amount'][$year]['quarts']['1 квартал']);
        $sheet->setCellValue('C' . $rowIndex, $this->info['total']['rate'][$year]['quarts']['1 квартал']);

        $sheet->setCellValue('D' . $rowIndex, $this->info['total']['amount'][$year]['quarts']['2 квартал']);
        $sheet->setCellValue('E' . $rowIndex, $this->info['total']['rate'][$year]['quarts']['2 квартал']);

        $sheet->setCellValue('F' . $rowIndex, $this->info['total']['amount'][$year]['quarts']['3 квартал']);
        $sheet->setCellValue('G' . $rowIndex, $this->info['total']['rate'][$year]['quarts']['3 квартал']);

        $sheet->setCellValue('H' . $rowIndex, $this->info['total']['amount'][$year]['quarts']['4 квартал']);
        $sheet->setCellValue('I' . $rowIndex, $this->info['total']['rate'][$year]['quarts']['4 квартал']);

        $rowIndex++;

        $sheet->setCellValue('A' . $rowIndex, 'Количество часов рабочих (по данным из CRM)');
        $sheet->getRowDimension($rowIndex)->setRowHeight(30);

        $sheet->setCellValue('B' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['1 квартал'], 0, '.', ' '));
        $sheet->setCellValue('C' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['1 квартал'], 0, '.', ' '));

        $sheet->setCellValue('D' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['2 квартал'], 0, '.', ' '));
        $sheet->setCellValue('E' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['2 квартал'], 0, '.', ' '));

        $sheet->setCellValue('F' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['3 квартал'], 0, '.', ' '));
        $sheet->setCellValue('G' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['3 квартал'], 0, '.', ' '));

        $sheet->setCellValue('H' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['4 квартал'], 0, '.', ' '));
        $sheet->setCellValue('I' . $rowIndex, number_format($this->info['rates'][$year]['quarts']['4 квартал'], 0, '.', ' '));

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->getColumnDimension('A')->setWidth(100);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);

        $sheet->getStyle('A1:I2')->getFont()->setBold(true);
        $sheet->getStyle('A' . ($rowIndex - 1) . ':I' . $rowIndex)->getFont()->setBold(true);

        $sheet->getStyle('A1:I2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('B' . $rowIndex . ':I' . $rowIndex)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:I' . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);

        $sheet->getStyle('B3:I'. ($rowIndex - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:I'. $rowIndex)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('A' . ($rowIndex - 1) . ':I' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
    }
}
