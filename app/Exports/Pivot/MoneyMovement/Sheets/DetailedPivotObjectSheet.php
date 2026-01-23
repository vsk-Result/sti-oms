<?php

namespace App\Exports\Pivot\MoneyMovement\Sheets;

use App\Models\Company;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DetailedPivotObjectSheet implements
    WithTitle,
    WithStyles
{
    private Builder $payments;

    public function __construct(Builder $payments)
    {
        $this->payments = $payments;
    }

    public function title(): string
    {
        return 'Детализированная сводная по объектам';
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $minDate = (clone $this->payments)->min('date');
        $maxDate = (clone $this->payments)->max('date');
        $period = [$minDate, $maxDate];

        $sheet->setCellValue('A2', 'Период с ' . Carbon::parse($minDate)->format('d.m.Y') . ' по ' . Carbon::parse($maxDate)->format('d.m.Y'));

        $sheet->setCellValue('A3', 'Остаток на начало ' . Carbon::parse($minDate)->format('d.m'));
        $sheet->setCellValue('B3', FinanceReportHistory::getBalanceForFinanceReportByDate($minDate));

        $sheet->setCellValue('A4', 'Итого приход');
        $sheet->setCellValue('B4', Payment::where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('company_id', Company::getSTI()->id)->whereBetween('date', $period)->where('amount', '>=', 0)->sum('amount'));

        $sheet->setCellValue('A5', 'Итого расход');
        $sheet->setCellValue('B5', Payment::where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('company_id', Company::getSTI()->id)->whereBetween('date', $period)->where('amount', '<', 0)->sum('amount'));

        $sheet->setCellValue('A6', 'Остаток на конец ' . Carbon::parse($maxDate)->format('d.m'));
        $sheet->setCellValue('B6', FinanceReportHistory::getBalanceForFinanceReportByDate($maxDate));

        $sheet->getStyle('A2:B2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(26);
        $sheet->getColumnDimension('B')->setWidth(17);

        $sheet->getStyle('A1:B7')->getFont()->setBold(true);
        $sheet->mergeCells('A2:B2');

        $sheet->getStyle('A2:B2')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $objectIds = (clone $this->payments)->where('type_id', Payment::TYPE_OBJECT)->groupBy('object_id')->pluck('object_id')->toArray();
        $objects = BObject::whereIn('id', $objectIds)->orderByDesc('code')->get();

        $row = 9;

        $organizationIds = (clone $this->payments)->where('amount', '<', 0)->select('organization_receiver_id')->groupBy('organization_receiver_id')->pluck('organization_receiver_id')->toArray();
        $organizations = Organization::whereIn('id', $organizationIds)->pluck('name', 'id')->toArray();

        $categoryTotal = [
            Payment::CATEGORY_RAD => 0,
            Payment::CATEGORY_MATERIAL => 0,
            Payment::CATEGORY_OPSTE => 0,
            Payment::CATEGORY_SALARY => 0,
            Payment::CATEGORY_TAX => 0,
            Payment::CATEGORY_CUSTOMERS => 0,
            Payment::CATEGORY_TRANSFER => 0,
        ];

        foreach ($objects as $object) {
            if ($object->code === '27.1') {
                continue;
            }

            $groupInfo = [];

            $addToRow = 0;
            foreach ($object->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '<', 0)->select('category', DB::raw('sum(amount) as sum_amount'))->groupBy('category')->get() as $payment) {
                $groupInfoItem = [
                    'name' => $payment->category,
                    'amount' => $payment->sum_amount,
                    'groupInfo' => [],
                ];

                $category = isset($categoryTotal[$payment->category]) ? $payment->category : Payment::CATEGORY_MATERIAL;

                $categoryTotal[$category] += $payment->sum_amount;

                $addToRow++;

                foreach ($object->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '<', 0)->where('category', $payment->category)->select('organization_receiver_id', DB::raw('sum(amount) as sum_amount'))->groupBy('organization_receiver_id')->get() as $payment) {
                    $groupInfoItem['groupInfo'][] = [
                        'name' => $organizations[$payment->organization_receiver_id] ?? 'Не определена_' . $payment->organization_receiver_id,
                        'amount' => $payment->sum_amount,
                    ];
                    $addToRow++;
                }

                $groupInfo[] = $groupInfoItem;
            }

            $this->fillObjectInfo($sheet, $row, [
                'title' => $object->getName(),
                'receive' => $object->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '>=', 0)->sum('amount'),
                'payment' => $object->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '<', 0)->sum('amount'),
                'period' => $period,
                'groupInfo' => $groupInfo
            ]);

            $row += 5 + $addToRow;
        }

        $row++;

        $office = BObject::where('code', '27.1')->first();

        $groupInfo = [];

        $addToRow = 0;
        foreach ($office->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '<', 0)->select('category', DB::raw('sum(amount) as sum_amount'))->groupBy('category')->get() as $payment) {
            $groupInfoItem = [
                'name' => $payment->category,
                'amount' => $payment->sum_amount,
                'groupInfo' => [],
            ];

            $category = isset($categoryTotal[$payment->category]) ? $payment->category : Payment::CATEGORY_MATERIAL;

            $categoryTotal[$category] += $payment->sum_amount;

            $addToRow++;

            foreach ($office->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '<', 0)->where('category', $payment->category)->select('organization_receiver_id', DB::raw('sum(amount) as sum_amount'))->groupBy('organization_receiver_id')->get() as $payment) {
                $groupInfoItem['groupInfo'][] = [
                    'name' => $organizations[$payment->organization_receiver_id] ?? 'Не определена_' . $payment->organization_receiver_id,
                    'amount' => $payment->sum_amount,
                ];
                $addToRow++;
            }

            $groupInfo[] = $groupInfoItem;
        }

        $this->fillObjectInfo($sheet, $row, [
            'title' => 'Офис',
            'receive' => $office->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '>=', 0)->sum('amount'),
            'payment' => $office->payments()->where('company_id', Company::getSTI()->id)->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->whereBetween('date', $period)->where('amount', '<', 0)->sum('amount'),
            'period' => $period,
            'groupInfo' => $groupInfo
        ]);

        $row += $addToRow + 5;

        $groupInfo = [];

        $addToRow = 0;
        foreach ((clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->where('amount', '<', 0)->select('category', DB::raw('sum(amount) as sum_amount'))->groupBy('category')->get() as $payment) {
            $groupInfoItem = [
                'name' => $payment->category,
                'amount' => $payment->sum_amount,
                'groupInfo' => [],
            ];

            $category = isset($categoryTotal[$payment->category]) ? $payment->category : Payment::CATEGORY_MATERIAL;

            $categoryTotal[$category] += $payment->sum_amount;

            $addToRow++;

            foreach ((clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->where('amount', '<', 0)->where('category', $payment->category)->select('organization_receiver_id', DB::raw('sum(amount) as sum_amount'))->groupBy('organization_receiver_id')->get() as $payment) {
                $groupInfoItem['groupInfo'][] = [
                    'name' => $organizations[$payment->organization_receiver_id] ?? 'Не определена_' . $payment->organization_receiver_id,
                    'amount' => $payment->sum_amount,
                ];
                $addToRow++;
            }

            $groupInfo[] = $groupInfoItem;
        }

        $this->fillObjectInfo($sheet, $row, [
            'title' => 'Общие затраты',
            'receive' => (clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->where('amount', '>=', 0)->sum('amount'),
            'payment' => (clone $this->payments)->where('type_id', Payment::TYPE_GENERAL)->where('amount', '<', 0)->sum('amount'),
            'period' => $period,
            'groupInfo' => $groupInfo
        ]);

        $row += $addToRow + 5;

        $groupInfo = [];

        $addToRow = 0;
        foreach ((clone $this->payments)->where('type_id', Payment::TYPE_TRANSFER)->where('amount', '<', 0)->select('category', DB::raw('sum(amount) as sum_amount'))->groupBy('category')->get() as $payment) {
            $groupInfoItem = [
                'name' => $payment->category,
                'amount' => $payment->sum_amount,
                'groupInfo' => [],
            ];

            $category = isset($categoryTotal[$payment->category]) ? $payment->category : Payment::CATEGORY_MATERIAL;

            $categoryTotal[$category] += $payment->sum_amount;

            $addToRow++;

            foreach ((clone $this->payments)->where('type_id', Payment::TYPE_TRANSFER)->where('amount', '<', 0)->where('category', $payment->category)->select('organization_receiver_id', DB::raw('sum(amount) as sum_amount'))->groupBy('organization_receiver_id')->get() as $payment) {
                $groupInfoItem['groupInfo'][] = [
                    'name' => $organizations[$payment->organization_receiver_id] ?? 'Не определена_' . $payment->organization_receiver_id,
                    'amount' => $payment->sum_amount,
                ];
                $addToRow++;
            }

            $groupInfo[] = $groupInfoItem;
        }

        $this->fillObjectInfo($sheet, $row, [
            'title' => 'Трансфер',
            'receive' => (clone $this->payments)->where('type_id', Payment::TYPE_TRANSFER)->where('amount', '>=', 0)->sum('amount'),
            'payment' => (clone $this->payments)->where('type_id', Payment::TYPE_TRANSFER)->where('amount', '<', 0)->sum('amount'),
            'period' => $period,
            'groupInfo' => $groupInfo
        ]);

        $row += $addToRow + 5;

        $sheet->getStyle('B3:B' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $totalCategorySum = array_sum($categoryTotal);

        $sheet->setCellValue('D2', 'Свод итогов по категориям по расходам');
        $sheet->setCellValue('F2', '%');

        $totalRow = 2;
        foreach ($categoryTotal as $catName => $catAmount) {
            if (!is_valid_amount_in_range($catAmount)) {
                continue;
            }

            $totalRow++;

            $sheet->setCellValue('D' . $totalRow, $catName);
            $sheet->setCellValue('E' . $totalRow, $catAmount);
            $sheet->setCellValue('F' . $totalRow, $totalCategorySum != 0 ? $catAmount / $totalCategorySum : 0);
        }

        $sheet->getStyle('D2:F2')->getAlignment()->setVertical('center')->setHorizontal('center')->setWrapText(false);
        $sheet->getRowDimension(2)->setRowHeight(30);

        $sheet->getColumnDimension('D')->setWidth(26);
        $sheet->getColumnDimension('E')->setWidth(17);

        $sheet->getStyle('D1:F' . $totalRow)->getFont()->setBold(true);
        $sheet->mergeCells('D2:E2');

        $sheet->getStyle('D2:F2')->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);

        $sheet->getStyle('E3:E' . $totalRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('F3:F' . $totalRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
    }

    public function fillObjectInfo(&$sheet, $row, array $info)
    {
        $addToRow = 0;
        $startRow = $row;

        $sheet->setCellValue('A' . $row, $info['title']);

        $sheet->getStyle('A' . $row)->getFont()->setBold(true);

        $row++;
        $sheet->setCellValue('A' . $row, 'Приход');
        $sheet->setCellValue('B' . $row, $info['receive']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Расход');
        $sheet->setCellValue('B' . $row, $info['payment']);
        $row++;

        if (isset($info['groupInfo'])) {
            foreach ($info['groupInfo'] as $groupInfo) {
                $sheet->setCellValue('A' . $row, '    ' . $groupInfo['name']);
                $sheet->setCellValue('B' . $row, $groupInfo['amount']);

                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setItalic(true);
                $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setName('Calibri')->setSize(10);


                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(false)
                    ->setCollapsed(true);

                $row++;

                $addToRow++;

                if (isset($groupInfo['groupInfo'])) {
                    foreach ($groupInfo['groupInfo'] as $groupInfoSub) {
                        $sheet->setCellValue('A' . $row, '        ' . $groupInfoSub['name']);
                        $sheet->setCellValue('B' . $row, $groupInfoSub['amount']);

                        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setItalic(true);
                        $sheet->getStyle('A' . $row . ':B' . $row)->getFont()->setName('Calibri')->setSize(9);

                        $sheet->getRowDimension($row)->setOutlineLevel(2)
                            ->setVisible(false)
                            ->setCollapsed(true);

                        $row++;

                        $addToRow++;
                    }
                }
            }
        }

        $sheet->setCellValue('A' . $row, 'Сальдо');
        $sheet->setCellValue('B' . $row, $info['receive'] + $info['payment']);

        $sheet->getStyle('A' . $startRow . ':B' . ($startRow + 3 + $addToRow))->applyFromArray([
            'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM]]
        ]);
    }
}
