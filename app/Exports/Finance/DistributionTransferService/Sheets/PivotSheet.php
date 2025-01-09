<?php

namespace App\Exports\Finance\DistributionTransferService\Sheets;

use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheet implements
    WithTitle,
    WithStyles,
    WithEvents
{
    private string $sheetName;

    public function __construct(string $sheetName)
    {
        $this->sheetName = $sheetName;
    }

    public function title(): string
    {
        return $this->sheetName;
    }

    public function styles(Worksheet $sheet): void
    {
        $codes = GeneralCost::getObjectCodesForGeneralCosts();
        $objects = BObject::whereIn('code', $codes)->orderByDesc('code')->get();

        $transferTotalAmount = 0;
        $transferInfo = [];

        $years = [];
        for ($i = 2021; $i <= Carbon::now()->year; $i++) {
            $years[] = $i;
        }
        $years = array_reverse($years, true);

        foreach ($years as $year) {
            $datesBetween = [$year . '-01-01', $year . '-12-31'];
            $cashAmount = \App\Models\Payment::query()
                ->whereBetween('date', $datesBetween)
                ->where('payment_type_id', \App\Models\Payment::PAYMENT_TYPE_CASH)
                ->where('amount', '<=', 0)
                ->whereIn('company_id', [1, 5])
                ->whereIn('object_id', $objects->pluck('id'))
                ->sum('amount');
            $transferAmount = \App\Models\Payment::query()
                ->whereBetween('date', $datesBetween)
                ->whereIn('company_id', [1, 5])
                ->where('code', '7.11')
                ->where('type_id', \App\Models\Payment::TYPE_GENERAL)
                ->sum('amount');

            $transferInfo[$year] = [
                'cash_amount' => $cashAmount,
                'transfer_amount' => $transferAmount,
                'info' => \App\Services\ObjectService::getDistributionTransferServiceByPeriod($datesBetween),
            ];

            $transferTotalAmount += $transferAmount;
        }

        $averagePercents = [];
        foreach($objects as $object) {
            $percentSum = 0;
            $percentCount = 0;

            foreach($transferInfo as $info) {
                if (isset($info['info'][$object->id])) {
                    $percent = ($info['info'][$object->id]['cash_amount'] < 0 ? abs($info['info'][$object->id]['transfer_amount'] / $info['info'][$object->id]['cash_amount']) : 0);
                    $percentSum += $percent;
                    $percentCount++;
                }
            }

            $averagePercents[$object->id] = $percentCount > 0 ? $percentSum / $percentCount : 0;
        }

        $columns = ['E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $sheet->setCellValue('A1', 'РАЗБИВКА УСЛУГ ПО ТРАНСФЕРУ ПО ГОДАМ');
        $sheet->setCellValue('B1', 'ИТОГО');
        $sheet->setCellValue('D1', $transferTotalAmount);

        $columnIndex = 0;
        foreach($transferInfo as $year => $info) {
            $sheet->setCellValue($columns[$columnIndex] . '1', $year);
            $sheet->setCellValue($columns[$columnIndex + 2] . '1', $info['transfer_amount']);
            $columnIndex += 3;
        }

        $sheet->getStyle('D1')->getFont()->setColor(new Color($transferTotalAmount < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

        $columnIndex = 2;
        foreach($transferInfo as $info) {
            $sheet->getStyle($columns[$columnIndex] . '1')->getFont()->setColor(new Color($info['transfer_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $columnIndex += 3;
        }

        $sheet->setCellValue('A2', 'Объект');
        $sheet->setCellValue('B2', 'Наличные расходы');
        $sheet->setCellValue('C2', '%');
        $sheet->setCellValue('D2', 'Услуги по трансферу');

        $columnIndex = 0;
        foreach($transferInfo as $info) {
            $sheet->setCellValue($columns[$columnIndex] . '2', 'Наличные расходы');
            $sheet->setCellValue($columns[$columnIndex + 1] . '2', '%');
            $sheet->setCellValue($columns[$columnIndex + 2] . '2', 'Услуги по трансферу');
            $columnIndex += 3;
        }

        $row = 3;
        foreach($objects as $object) {
            $sheet->setCellValue('A' . $row, $object->getName());

            $totalCash = 0;
            $totalTransfer = 0;
            foreach($transferInfo as $info) {
                $totalCash += ($info['info'][$object->id]['cash_amount'] ?? 0);
                $totalTransfer += ($info['info'][$object->id]['transfer_amount'] ?? 0);
            }

            $sheet->setCellValue('B' . $row,$totalCash);
            $sheet->setCellValue('C' . $row, $totalCash < 0 ? abs($totalTransfer / $totalCash) : 0);
            $sheet->setCellValue('D' . $row, $totalTransfer);

            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color($totalCash < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
            $sheet->getStyle('D' . $row)->getFont()->setColor(new Color($totalTransfer < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex = 0;
            foreach($transferInfo as $info) {
                if (isset ($info['info'][$object->id])) {
                    $sheet->setCellValue($columns[$columnIndex] . $row, $info['info'][$object->id]['cash_amount']);
                    $sheet->setCellValue($columns[$columnIndex + 1] . $row, $info['info'][$object->id]['cash_amount'] < 0 ? abs($info['info'][$object->id]['transfer_amount'] / $info['info'][$object->id]['cash_amount']) : 0);
                    $sheet->setCellValue($columns[$columnIndex + 2] . $row, $info['info'][$object->id]['transfer_amount']);

                    $sheet->getStyle($columns[$columnIndex] . $row)->getFont()->setColor(new Color($info['info'][$object->id]['cash_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                    $sheet->getStyle($columns[$columnIndex + 2] . $row)->getFont()->setColor(new Color($info['info'][$object->id]['transfer_amount'] < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));
                }

                $columnIndex += 3;
            }

            $sheet->getRowDimension($row)->setRowHeight(50);
            $row++;
        }

        $row--;

        $sheet->getRowDimension(1)->setRowHeight(50);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(43);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        foreach($columns as $column) {
            $sheet->getColumnDimension($column)->setWidth(20);
        }

        $sheet->getStyle('A1:AN' . $row)->getAlignment()->setVertical('center')->setHorizontal('right')->setWrapText(true);

        $sheet->getStyle('A1:A' . $row)->getAlignment()->setHorizontal('left');
        $sheet->getStyle('B1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('E1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('H1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('K1')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('N1')->getAlignment()->setHorizontal('left');

        $sheet->getStyle('F1:F' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('I1:I' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('L1:L' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('O1:O' . $row)->getAlignment()->setHorizontal('center');

        $sheet->getStyle('B2:AN2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C3:C' . $row)->getAlignment()->setHorizontal('center');

        $sheet->getStyle('A1:AN1')->getFont()->setBold(true);
        $sheet->getStyle('B1:D' . $row)->getFont()->setBold(true);

        $sheet->getStyle('A1:P2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');
        $sheet->getStyle('B1:D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:P' . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'dddddd']]]
        ]);

        $sheet->getStyle('B1:D' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('E1:G' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('H1:J' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('K1:M' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('N1:P' . $row)->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'f15a22']]]
        ]);

        $sheet->getStyle('B1:AN' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C3:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('F3:F' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('I3:I' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('L3:L' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('O3:O' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->freezePane('B2');
            },
        ];
    }
}
