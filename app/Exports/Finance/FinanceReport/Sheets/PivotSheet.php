<?php

namespace App\Exports\Finance\FinanceReport\Sheets;

use App\Models\PaymentImport;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
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
        $sheet->getPageSetup()->setPrintAreaByColumnAndRow(1, 1, 7, 28);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        // Баланс по счетам
        $this->fillAccounts($sheet);

        // Баланс по депозитам
        $this->fillDeposites($sheet);

        // Долг по кредитам
        $this->fillCredits($sheet);

        // Долг по займам
        $this->fillLoans($sheet);
    }

    private function fillAccounts(&$sheet): void
    {
        $balancesInfo = $this->info['balancesInfo'];
        $sheet->setCellValue('A1', "Балансы" . "\n" . "Последняя выписка загружена " . $balancesInfo->dateLastStatement);
        $row = 2;

        foreach($balancesInfo->balances->banks as $bankName => $balance) {
            if ($bankName === 'ПАО "Росбанк"' || $bankName === 'ПАО "МКБ"' || $bankName === 'АО "АЛЬФА БАНК"') {
                continue;
            }

            $sheet->setCellValue('A' . $row, $bankName);
            $sheet->setCellValue('B' . $row, $balance->RUB);

            if ($balance->EUR !== 0) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $row++;

                $sheet->setCellValue('A' . $row, $bankName . ' (EUR)');
                $sheet->setCellValue('B' . $row, $balance->EUR);
            }

            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $row--;

        $sheet->setCellValue('A' . ($row + 1), 'ИТОГО RUB');
        $sheet->setCellValue('A' . ($row + 2), 'ИТОГО EUR');

        $sheet->setCellValue('B' . ($row + 1), $balancesInfo->balances->total->RUB);
        $sheet->setCellValue('B' . ($row + 2), $balancesInfo->balances->total->EUR);

        $sheet->getStyle('B' . ($row + 1))->getFont()->setColor(new Color($balancesInfo->balances->total->RUB < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
        $sheet->getStyle('B' . ($row + 2))->getFont()->setColor(new Color($balancesInfo->balances->total->EUR < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $row += 2;

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getRowDimension($row - 1)->setRowHeight(30);
        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(20);
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
        $depositsInfo = $this->info['depositsInfo'];
        $row = collect($this->info['balancesInfo']->balances->banks)->count() + 6;
        $rowTitle = $row;
        $sheet->setCellValue('A' . $rowTitle, 'Баланс по депозитам');

        $row ++;
        foreach($depositsInfo->deposits as $name => $deposit) {
            $sheet->setCellValue('A' . $row, $name);
            $sheet->setCellValue('B' . $row, $deposit);
            $sheet->getRowDimension($row)->setRowHeight(25);
            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($deposit < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $row++;
        }

        $sheet->setCellValue('A' . $row, 'ИТОГО');
        $sheet->setCellValue('B' . $row, $depositsInfo->totalDepositsAmount);
        $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($depositsInfo->totalDepositsAmount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

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
        $creditsInfo = $this->info['creditsInfo'];

        $sheet->setCellValue('D1', 'Долг по кредитам');
        $sheet->setCellValue('D2', 'Банк / Договор');
        $sheet->setCellValue('E2', 'Доступно');
        $sheet->setCellValue('F2', 'В использовании');
        $sheet->setCellValue('G2', 'Всего');

        $row = 3;
        foreach($creditsInfo->credits as $credit) {
            $sheet->setCellValue('D' . $row, $credit->bank . "\n" . $credit->contract);
            $sheet->setCellValue('E' . $row, $credit->total - $credit->used);
            $sheet->setCellValue('F' . $row, $credit->used);
            $sheet->setCellValue('G' . $row, $credit->total);
            $sheet->getRowDimension($row)->setRowHeight(30);
            $row++;
        }

        $sheet->setCellValue('D' . $row, 'ДОЛГ');
        $sheet->setCellValue('E' . $row, $creditsInfo->totalCreditAmount);
        $sheet->getStyle('E' . $row)->getFont()->setColor(new Color($creditsInfo->totalCreditAmount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

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
        $loansInfo = $this->info['loansInfo'];
        $creditsInfo = $this->info['creditsInfo'];

        $row = collect($creditsInfo->credits)->count() + 6;
        $rowTitle = $row;
        $sheet->setCellValue('D' . $rowTitle, 'Баланс по займам');

        $row ++;
        foreach($loansInfo->loans as $loan) {
            $sheet->setCellValue('D' . $row, $loan->organization->name);
            $sheet->setCellValue('E' . $row, $loan->amount);
            $sheet->getStyle('E' . $row)->getFont()->setColor(new Color($loan->amount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

        $sheet->setCellValue('D' . $row, 'ИТОГО RUB');
        $sheet->setCellValue('E' . $row, $loansInfo->totalLoanAmount );
        $sheet->getStyle('E' . $row)->getFont()->setColor(new Color($loansInfo->totalLoanAmount  < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

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
}
