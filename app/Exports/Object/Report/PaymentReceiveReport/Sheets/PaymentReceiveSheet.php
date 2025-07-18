<?php

namespace App\Exports\Object\Report\PaymentReceiveReport\Sheets;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentReceiveSheet implements
    WithTitle,
    WithStyles,
    ShouldAutoSize
{
    public function __construct(private array $reportInfo) {}

    public function title(): string
    {
        return 'Отчет доходов и расходов';
    }

    public function styles(Worksheet $sheet): void
    {
        $reportInfo = $this->reportInfo;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(40);

        $sheet->getStyle('A1:O2')->getFont()->setBold(true);
        $sheet->getStyle('A1:O2')->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->setCellValue('A1', 'Отчет доходов и расходов');
        $sheet->setCellValue('A2', 'Заказчик');
        $sheet->setCellValue('B2', 'Категория');

        $sheet->mergeCells('A1:B1');

        $row = 1;
        $columnIndex = 3;
        foreach($this->reportInfo['monthsFull'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . $row, $month);
            $sheet->setCellValue($column . $row + 1, 'Сумма');
            $sheet->getColumnDimension($column)->setWidth(30);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, 'Накопительные');
        $sheet->setCellValue($column . $row + 1, 'Сумма');
        $sheet->getColumnDimension($column)->setWidth(30);

        $row += 2;
        $sheet->setCellValue('A' . $row, 'КС 2');
        $sheet->setCellValue('B' . $row, 'Материал');
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['receiveInfo']['material'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['receiveInfo']['material'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Работы');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['receiveInfo']['rad'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['receiveInfo']['rad'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Накладные');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['receiveInfo']['service'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['receiveInfo']['service'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Итого выручка:');
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':' . $column . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['receiveInfo']['total'][2025][$month];

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['receiveInfo']['total'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Расходная часть	');
        $sheet->setCellValue('B' . $row, '');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getRowDimension($row)->setRowHeight(50);
        $row++;

        $sheet->setCellValue('A' . $row, 'Подрядчики');
        $sheet->setCellValue('B' . $row, 'Материал');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['contractors']['material'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['contractors']['material'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Работы');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['contractors']['rad'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['contractors']['rad'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Поставщики');
        $sheet->setCellValue('B' . $row, 'Материал');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['providers']['material'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['providers']['material'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Услуги/накладные');
        $sheet->setCellValue('B' . $row, 'Содержание стройплащадки');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['service']['service'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['service']['service'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Зарплата рабочие');
        $sheet->setCellValue('B' . $row, '');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['salary_workers'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['salary_workers'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Зарплата ИТР');
        $sheet->setCellValue('B' . $row, '');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['salary_itr'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['salary_itr'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Налоги с зп');
        $sheet->setCellValue('B' . $row, '');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['salary_taxes'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['salary_taxes'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Услуги трансфера');
        $sheet->setCellValue('B' . $row, '');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['transfer'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['transfer'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Общие затраты (в т.ч офис)');
        $sheet->setCellValue('B' . $row, '');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['general_costs'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['general_costs'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, 'Налоги (НДС,прибыль)');
        $sheet->setCellValue('B' . $row, '');
        $sheet->getRowDimension($row)->setRowHeight(50);

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['accrued_taxes'][2025][$month] ?? 0;

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['accrued_taxes'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Итого расходы:');
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':' . $column . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['paymentInfo']['total'][2025][$month];

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['paymentInfo']['total'][2025]['total']);
        $row++;

        $sheet->setCellValue('A' . $row, '');
        $sheet->setCellValue('B' . $row, 'Маржа:');
        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row . ':' . $column . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':' . $column . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $columnIndex = 3;
        foreach($reportInfo['months'] as $month) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $reportInfo['receiveInfo']['total'][2025][$month] + $reportInfo['paymentInfo']['total'][2025][$month];

            if ($amount == 0) {
                $amount = '';
            }

            $sheet->setCellValue($column . $row, $amount);
            $columnIndex++;
        }

        $column = $this->getColumnWord($columnIndex);
        $sheet->setCellValue($column . $row, $reportInfo['receiveInfo']['total'][2025]['total'] + $reportInfo['paymentInfo']['total'][2025]['total']);
        $sheet->getStyle('A' . $row . ':' . $column . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('C3:' . $column . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A1:' . $column . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $sheet->getStyle('A1:' . $column . $row)->getAlignment()->setVertical('center');


//        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, $column, $row);
//        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
//        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
//        $sheet->getPageSetup()->setFitToWidth(1);
//        $sheet->getPageSetup()->setFitToHeight(1);
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
