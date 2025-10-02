<?php

namespace App\Exports\Pivot\Debt\Sheets;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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

    private array $pivot;

    public function __construct(string $sheetName, array $pivot)
    {
        $this->sheetName = $sheetName;
        $this->pivot = $pivot;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $pivot = $this->pivot;
        $lastColumn = $this->getColumnWord((2 + count($pivot['organizations'])));

        $sheet->setCellValue('A1', 'Контрагент');
        $sheet->setCellValue('B1', 'Итого');

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationName => $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . '1', $organizationName);
            $sheet->getColumnDimension($this->getColumnWord($columnIndex))->setWidth(25);
            $columnIndex++;
        }

        $row = 2;

        $sheet->setCellValue('A' . $row, 'Итого');
        $sheet->setCellValue('B' . $row, $pivot['total']['total']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $organizationInfo['total']);
            $columnIndex++;
        }

        $row++;

        $sheet->setCellValue('A' . $row, 'Долг по подрядчикам');
        $sheet->setCellValue('B' . $row, $pivot['total']['contractors']['total']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['contractors']['total']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, '        Неотработанный аванс');
        $sheet->setCellValue('B' . $row, $pivot['total']['contractors']['unwork_avans']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['contractors']['unwork_avans']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setItalic(true);
        $row++;

        $sheet->setCellValue('A' . $row, '        Гарантийное удержание');
        $sheet->setCellValue('B' . $row, $pivot['total']['contractors']['guarantee']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['contractors']['guarantee']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setItalic(true);
        $row++;

        $sheet->setCellValue('A' . $row, '        Гарантийное удержание (в т.ч. срок наступил)');
        $sheet->setCellValue('B' . $row, $pivot['total']['contractors']['guarantee_deadline']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['contractors']['guarantee_deadline']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setItalic(true);
        $row++;

        $sheet->setCellValue('A' . $row, '        Авансы к оплате');
        $sheet->setCellValue('B' . $row, $pivot['total']['contractors']['avans']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['contractors']['avans']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $row++;

        $sheet->setCellValue('A' . $row, '        Долг за СМР');
        $sheet->setCellValue('B' . $row, $pivot['total']['contractors']['amount']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['contractors']['amount']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setItalic(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'Долг поставщикам');
        $sheet->setCellValue('B' . $row, $pivot['total']['providers']['total']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['providers']['total']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, '        Фиксированная сумма');
        $sheet->setCellValue('B' . $row, $pivot['total']['providers']['amount_fix']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['providers']['amount_fix']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setItalic(true);
        $row++;

        $sheet->setCellValue('A' . $row, '        Изменяемая сумма');
        $sheet->setCellValue('B' . $row, $pivot['total']['providers']['amount_float']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['providers']['amount_float']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setItalic(true);
        $row++;

        $sheet->setCellValue('A' . $row, 'Долг за услуги');
        $sheet->setCellValue('B' . $row, $pivot['total']['service']['total']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['service']['total']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(50);
        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue('A' . $row, '        Сумма долга');
        $sheet->setCellValue('B' . $row, $pivot['total']['service']['amount']);

        $columnIndex = 3;
        foreach($pivot['organizations'] as $organizationInfo) {
            $sheet->setCellValue($this->getColumnWord($columnIndex) . $row, $this->formatAmount($organizationInfo['service']['amount']));
            $columnIndex++;
        }

        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getStyle('B' . $row)->getFont()->setItalic(true);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(70);
        $sheet->getRowDimension(2)->setRowHeight(50);
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(20);

        $sheet->getStyle('A1:' . $lastColumn . '2')->getFont()->setBold(true);
        $sheet->getStyle('B1:B' . $row)->getFont()->setBold(true);

        $sheet->getStyle('B1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getStyle('A1:' .$lastColumn . $row)->getAlignment()->setVertical('center')->setWrapText(true);

        $sheet->getStyle('B2:' . $lastColumn . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getStyle('A1:' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'aaaaaa']]]
        ]);

        $sheet->getStyle('B1:B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getStyle('A2:' . $lastColumn . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
    }

    private function getColumnWord($n) {
        return Coordinate::stringFromColumnIndex($n);
    }

    public function formatAmount($amount)
    {
        return is_valid_amount_in_range($amount) ? $amount : '-';
    }
}
