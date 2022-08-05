<?php

namespace App\Exports\Finance\FinanceReport\Sheets;

use App\Models\PaymentImport;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
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
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Баланс по счетам
        $this->fillAccounts($sheet);

        // Баланс по депозитам
        $this->fillDeposites($sheet);

        // Долг по кредитам
        $this->fillCredits($sheet);

        // Долг по займам
        $this->fillLoans($sheet);

        // Сводная по объектам
        $this->fillObjects($sheet);

//        $sheet->setCellValue('A1', 'Объект');
//        $sheet->setCellValue('B1', 'Итого');
//        $sheet->setCellValue('C1', 'Сумма неоплаченных работ по актам');
//        $sheet->setCellValue('D1', 'Аванс к получению');
//        $sheet->setCellValue('E1', 'Гарантийное удержание');
//
//        $sheet->setCellValue('A2', 'Итого');
//        $sheet->setCellValue('B2', $this->pivot['total']['acts'][$this->currency] + $this->pivot['total']['avanses'][$this->currency] + $this->pivot['total']['gu'][$this->currency]);
//        $sheet->setCellValue('C2', $this->pivot['total']['acts'][$this->currency]);
//        $sheet->setCellValue('D2', $this->pivot['total']['avanses'][$this->currency]);
//        $sheet->setCellValue('E2', $this->pivot['total']['gu'][$this->currency]);
//
//        $sheet->getStyle('B2')->getFont()->setColor(new Color(($this->pivot['total']['acts'][$this->currency] + $this->pivot['total']['avanses'][$this->currency] + $this->pivot['total']['gu'][$this->currency]) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//        $sheet->getStyle('C2')->getFont()->setColor(new Color($this->pivot['total']['acts'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//        $sheet->getStyle('D2')->getFont()->setColor(new Color($this->pivot['total']['avanses'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//        $sheet->getStyle('E2')->getFont()->setColor(new Color($this->pivot['total']['gu'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//
//        $rowIndex = 3;
//        foreach ($this->pivot['entries'] as $entry) {
//            $sheet->setCellValue('A' . $rowIndex, $entry['object']['name']);
//            $sheet->setCellValue('B' . $rowIndex, $entry['acts'][$this->currency] + $entry['avanses'][$this->currency] + $entry['gu'][$this->currency]);
//            $sheet->setCellValue('C' . $rowIndex, $entry['acts'][$this->currency] === 0 ? '' : $entry['acts'][$this->currency]);
//            $sheet->setCellValue('D' . $rowIndex, $entry['avanses'][$this->currency] === 0 ? '' : $entry['avanses'][$this->currency]);
//            $sheet->setCellValue('E' . $rowIndex, $entry['gu'][$this->currency] === 0 ? '' : $entry['gu'][$this->currency]);
//
//            $sheet->getStyle('B' . $rowIndex)->getFont()->setColor(new Color(($entry['acts'][$this->currency] + $entry['avanses'][$this->currency] + $entry['gu'][$this->currency]) < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//            $sheet->getStyle('C' . $rowIndex)->getFont()->setColor(new Color($entry['acts'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//            $sheet->getStyle('D' . $rowIndex)->getFont()->setColor(new Color($entry['avanses'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//            $sheet->getStyle('E' . $rowIndex)->getFont()->setColor(new Color($entry['gu'][$this->currency] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
//
//            $sheet->getRowDimension($rowIndex)->setRowHeight(20);
//            $rowIndex++;
//        }
//
//        $rowIndex--;
//
//        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
//
//        $sheet->getRowDimension(1)->setRowHeight(30);
//        $sheet->getRowDimension(2)->setRowHeight(20);
//        $sheet->getColumnDimension('A')->setWidth(40);
//        $sheet->getColumnDimension('B')->setWidth(20);
//        $sheet->getColumnDimension('C')->setWidth(40);
//        $sheet->getColumnDimension('D')->setWidth(30);
//        $sheet->getColumnDimension('E')->setWidth(30);
//
//        $sheet->getStyle('A1:B2')->getFont()->setBold(true);
//        $sheet->getStyle('B1:B' . $rowIndex)->getFont()->setBold(true);
//
//        $sheet->getStyle('B1:E1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
//        $sheet->getStyle('A1:E' . $rowIndex)->getAlignment()->setVertical('center')->setWrapText(false);
//
//        $sheet->getStyle('B2:E'. $rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//
//        $sheet->getStyle('A1:E'. $rowIndex)->applyFromArray([
//            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
//        ]);
//
//        $sheet->getStyle('B1:B' . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
//        $sheet->getStyle('A2:E2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
//
//        $sheet->setAutoFilter('A1:E' . $rowIndex);
    }

    private function fillAccounts(&$sheet): void
    {
        $dateLastStatement = PaymentImport::orderByDesc('date')->first()->created_at->format('d.m.Y H:i');
        $sheet->setCellValue('A1', "Остатки на счетах" . "\n" . "(Последняя выписка загружена " . $dateLastStatement . ")");
        $row = 2;

        foreach($this->info['balances']['banks'] as $bankName => $balance) {
            if ($bankName === 'ПАО "Росбанк"') {
                continue;
            }

            $sheet->setCellValue('A' . $row, $bankName);

            if ($bankName === 'ПАО "МКБ"') {

                $balance = 11000;
                $sheet->setCellValue('B' . $row, $balance);

            } else {

                $sheet->setCellValue('B' . $row, $balance['RUB']);

                if ($balance['EUR'] !== 0) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                    $row++;

                    $sheet->setCellValue('A' . $row, $bankName . ' (EUR)');
                    $sheet->setCellValue('B' . $row, $balance['EUR']);
                }
            }

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $row--;

        $sheet->setCellValue('A' . ($row + 1), 'ИТОГО RUB');
        $sheet->setCellValue('A' . ($row + 2), 'ИТОГО EUR');

        $sheet->setCellValue('B' . ($row + 1), $this->info['balances']['total']['RUB']);
        $sheet->setCellValue('B' . ($row + 2), $this->info['balances']['total']['EUR']);

        $row += 2;

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getRowDimension($row - 1)->setRowHeight(30);
        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('B2:B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A1:B1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A2:A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('B2:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(false);
        $sheet->mergeCells('A1:B1');

        $sheet->getStyle('B2:B'. $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A' . ($row - 1) . ':B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffbe90');
        $sheet->getStyle('A1:B'. $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    private function fillDeposites(&$sheet): void
    {
        $row = count($this->info['balances']['banks']) + 6;
        $rowTitle = $row;
        $sheet->setCellValue('A' . $rowTitle, 'Баланс по депозитам');

        $row ++;
        foreach($this->info['deposites']['deposites'] as $currency => $deposit) {
            $sheet->setCellValue('A' . $row, $currency);
            $sheet->setCellValue('B' . $row, $deposit);
            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $sheet->setCellValue('A' . $row, 'ИТОГО RUB');
        $sheet->setCellValue('B' . $row, $this->info['deposites']['totalAmount']);

        $sheet->getRowDimension($rowTitle)->setRowHeight(50);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('A' . $rowTitle)->getFont()->setBold(true);
        $sheet->getStyle('B' . $rowTitle . ':B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowTitle . ':B' . $rowTitle)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('A' . ($rowTitle + 1) . ':A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('B' . ($rowTitle + 1) . ':B' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(false);
        $sheet->mergeCells('A' . $rowTitle . ':B' . $rowTitle);

        $sheet->getStyle('B' . ($rowTitle + 1) . ':B'. $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffbe90');
        $sheet->getStyle('A' . $rowTitle . ':B'. $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    private function fillCredits(&$sheet): void
    {
        $sheet->setCellValue('D1', 'Долг по кредитам');
        $sheet->setCellValue('D2', 'Банк / Договор');
        $sheet->setCellValue('E2', 'Доступно');
        $sheet->setCellValue('F2', 'В использовании');
        $sheet->setCellValue('G2', 'Всего');

        $row = 3;
        foreach($this->info['credits']['credits'] as $credit) {
            $sheet->setCellValue('D' . $row, $credit['bank'] . "\n" . $credit['contract']);
            $sheet->setCellValue('E' . $row, abs($credit['sent']));
            $sheet->setCellValue('F' . $row, $credit['amount'] - abs($credit['sent']));
            $sheet->setCellValue('G' . $row, $credit['amount']);
            $sheet->getRowDimension($row)->setRowHeight(30);
            $row++;
        }

        $sheet->setCellValue('D' . $row, 'ДОЛГ');
        $sheet->setCellValue('E' . $row, $this->info['credits']['totalAmount']);

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getColumnDimension('D')->setWidth(35);
        $sheet->getColumnDimension('E')->setWidth(17);
        $sheet->getColumnDimension('F')->setWidth(17);
        $sheet->getColumnDimension('G')->setWidth(17);
        $sheet->getStyle('D1')->getFont()->setBold(true);
        $sheet->getStyle('D1:G2')->getFont()->setBold(true);
        $sheet->getStyle('E1:G' . $row)->getFont()->setBold(true);
        $sheet->getStyle('D1:G2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('D3:D' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(true);
        $sheet->getStyle('E3:G' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(false);
        $sheet->mergeCells('D1:G1');
        $sheet->mergeCells('E' . $row . ':G' . $row);

        $sheet->getStyle('E3' . ':G'. $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('D' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffbe90');
        $sheet->getStyle('D1' . ':G'. $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    private function fillLoans(&$sheet): void
    {
        $row = count($this->info['credits']['credits']) + 6;
        $rowTitle = $row;
        $sheet->setCellValue('D' . $rowTitle, 'Баланс по займам');

        $row ++;
        foreach($this->info['loans']['loans'] as $organizationName => $loan) {
            $sheet->setCellValue('D' . $row, $organizationName);
            $sheet->setCellValue('E' . $row, $loan);
            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $sheet->setCellValue('D' . $row, 'ИТОГО RUB');
        $sheet->setCellValue('E' . $row, $this->info['loans']['totalAmount']);

        $sheet->getRowDimension($rowTitle)->setRowHeight(50);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('D' . $rowTitle)->getFont()->setBold(true);
        $sheet->getStyle('E' . $rowTitle . ':E' . $row)->getFont()->setBold(true);
        $sheet->getStyle('D' . $rowTitle . ':E' . $rowTitle)->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(true);
        $sheet->getStyle('D' . ($rowTitle + 1) . ':D' . $row)->getAlignment()->setVertical('center')->setHorizontal('left')->setWrapText(false);
        $sheet->getStyle('E' . ($rowTitle + 1) . ':E' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(false);
        $sheet->mergeCells('D' . $rowTitle . ':E' . $rowTitle);

        $sheet->getStyle('E' . ($rowTitle + 1) . ':E'. $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('D' . $row . ':E' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('ffbe90');
        $sheet->getStyle('D' . $rowTitle . ':E'. $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    private function fillObjects(&$sheet): void
    {

    }
}
