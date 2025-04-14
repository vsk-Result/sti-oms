<?php

namespace App\Exports\Pivot\CashFlow\Sheets;

use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\PlanPaymentService;
use App\Services\ReceivePlanService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PivotSheetTwo implements
    WithTitle,
    WithStyles
{
    public function __construct(
        private ReceivePlanService $receivePlanService,
        private AccountBalanceService $accountBalanceService,
        private array $requestData
    ) {}

    public function title(): string
    {
        return 'Отчет';
    }

    public function styles(Worksheet $sheet): void
    {
        $periods = $this->receivePlanService->getPeriods(null, $this->requestData['period'] ?? null);

        $cfPayments = $this->receivePlanService->getCFPaymentsForAll($periods);
        $accounts = $this->accountBalanceService->getCurrentAccounts();

        $lastColumnIndex = 3 + count($periods);
        $lastColumn = $this->getColumnWord($lastColumnIndex);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(60);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension($lastColumn)->setWidth(30);

        $sheet->getStyle('A1:' . $lastColumn . '1')->getFont()->setBold(true);

        $sheet->getStyle('B1:' . $lastColumn . '1')->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->setCellValue('A1', 'Остаток денежных средств на начало дня на счетах: ' . Carbon::now()->format('d.m.Y'));
        $sheet->setCellValue('B1', 'Сумма');

        $row = 2;
        foreach ($accounts as $accountName => $amount) {
            $sheet->setCellValue('A' . $row, '         ' . $accountName);
            $sheet->setCellValue('B' . $row, $amount);
            $sheet->setCellValue($lastColumn . $row, $amount);

            $sheet->getRowDimension($row)->setRowHeight(25);

            $row++;
        }

        $sheet->getStyle('B2:B' . $lastColumn . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');

        $planPaymentGroups = PlanPaymentGroup::all();
        $CFPlanPayments = PlanPayment::all();
        $otherPlanPayments = PlanPayment::getOther();
        $CFPlanPaymentEntries = PlanPaymentEntry::all();
        $reasons = ReceivePlan::getReasons();
        $plans = $this->receivePlanService->getPlans(null, $periods[0]['start'], end($periods)['start']);

        $activeObjectIds = BObject::active()->orderBy('code')->pluck('id')->toArray();
        $closedObjectIds = ReceivePlan::whereBetween('date', [$periods[0]['start'], end($periods)['start']])->groupBy('object_id')->pluck('object_id')->toArray();

        $objects = BObject::whereIn('id', array_merge($activeObjectIds, $closedObjectIds))->get();

        $row = count($accounts) + 2;

        $sheet->setCellValue('A' . $row, '     ПОСТУПЛЕНИЯ ИТОГО, в том числе:');
        $sheet->setCellValue('B' . $row, 'Код объекта');
        $sheet->setCellValue('A' . $row + 1, '         Целевые авансы');
        $sheet->setCellValue('A' . $row + 2, '         Прочие поступления');

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getRowDimension($row + 1)->setRowHeight(25);
        $sheet->getRowDimension($row + 2)->setRowHeight(25);

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getStyle('A' . ($row + 1) . ':'. $lastColumn . ($row + 2))->getFont()->setItalic(true);
        $sheet->getStyle('A' . ($row + 1) . ':'. $lastColumn . ($row + 2))->getFont()->setSize(11);

        $columnIndex = 3;
        foreach($periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $sheet->setCellValue($column . '1', $period['format']);
            $sheet->getColumnDimension($column)->setWidth(30);
            $columnIndex++;
        }

        $sheet->setCellValue($lastColumn . '1', 'ИТОГО');

        $total = 0;
        $targetAvansTotal = 0;
        $otherTotal = 0;
        $columnIndex = 3;
        foreach($periods as $period) {
            $column = $this->getColumnWord($columnIndex);
            $amount = $plans->where('date', $period['start'])->sum('amount');
            $targetAvansAmount = $plans->where('date', $period['start'])->where('reason_id', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            $total += $amount;
            $targetAvansTotal += $targetAvansAmount;
            $otherTotal += $otherAmount;

            $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
            $sheet->setCellValue($column . ($row + 1), $targetAvansAmount != 0 ? $targetAvansAmount : '');
            $sheet->setCellValue($column . ($row + 2), $otherAmount != 0 ? $otherAmount : '');

            $columnIndex++;
        }

        $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
        $sheet->setCellValue($lastColumn . ($row + 1), $targetAvansTotal != 0 ? $targetAvansTotal : '');
        $sheet->setCellValue($lastColumn . ($row + 2), $otherTotal != 0 ? $otherTotal : '');

        $row = $row + 3;

        $sheet->getRowDimension($row)->setRowHeight(5);
        $row++;

        $officeObjectId = BObject::where('code', '27.1')->first()->id;

        $sheet->setCellValue('A' . $row, '27.1');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $total = 0;
        $columnIndex = 3;
        foreach($periods as $period) {

            $column = $this->getColumnWord($columnIndex);
            $amount = $cfPayments['objects'][$officeObjectId][$period['start']]['total'] ?? 0;
            $total += $amount;

            $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
            $columnIndex++;
        }

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
        $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
        $row++;


        $planGroupedPaymentAmount = [];
        foreach ($planPaymentGroups as $group) {
            if ($group->payments->count() === 0) {
                continue;
            }
            $planGroupedPaymentAmount[$group->name] = [];

            foreach ($group->payments as $payment) {
                foreach($periods as $index => $period) {
                    if ($index === 0) {
                        $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                    } else {
                        $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                    }

                    if (! isset($planGroupedPaymentAmount[$group->name][$period['id']])) {
                        $planGroupedPaymentAmount[$group->name][$period['id']] = 0;
                    }
                    $planGroupedPaymentAmount[$group->name][$period['id']] += $amount;
                }
            }
        }

        foreach($planPaymentGroups as $group) {
            if ($group->payments->count() === 0) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $group->name . ' ИТОГО:');
            $sheet->setCellValue('B' . $row, $group->object->code ?? '');
            $sheet->getRowDimension($row)->setRowHeight(30);

            $groupTotal = 0;
            $columnIndex = 3;
            foreach($periods as $period) {
                $column = $this->getColumnWord($columnIndex);
                $amount = $planGroupedPaymentAmount[$group->name][$period['id']];
                $groupTotal += $amount;

                $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
                $columnIndex++;
            }

            $sheet->setCellValue($lastColumn . $row, $groupTotal != 0 ? $groupTotal : '');
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

            $row++;

            foreach($group->payments as $payment) {
                $total = $payment->entries->where('date', '<=', last($periods)['end'])->sum('amount');

                if (!is_valid_amount_in_range($total)) {
                    continue;
                }

                $sheet->setCellValue('A' . $row, '        ' . $payment->name);
                $sheet->setCellValue('B' . $row, $payment->object->code ?? '');
                $sheet->getRowDimension($row)->setRowHeight(30);

                $columnIndex = 3;
                foreach($periods as $index => $period) {

                    $column = $this->getColumnWord($columnIndex);
                    if ($index === 0) {
                        $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                    } else {
                        $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                    }

                    $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
                    $columnIndex++;
                }

                $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
                $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setSize(11);
                $sheet->getStyle('A' . $row . ':'. $lastColumn . $row)->getFont()->setItalic(true);

                $sheet->getRowDimension($row)->setOutlineLevel(1)
                    ->setVisible(true)
                    ->setCollapsed(false);
                $row++;
            }
        }

        foreach($CFPlanPayments as $payment) {
            if (!is_null($payment->group_id)) {
                continue;
            }

            $total = $payment->entries->where('date', '<=', last($periods)['end'])->sum('amount');

            if (!is_valid_amount_in_range($total)) {
                continue;
            }

            $sheet->setCellValue('A' . $row, $payment->name);
            $sheet->setCellValue('B' . $row, $payment->object->code ?? '');
            $sheet->getRowDimension($row)->setRowHeight(30);

            $columnIndex = 3;
            foreach($periods as $index => $period) {

                $column = $this->getColumnWord($columnIndex);
                if ($index === 0) {
                    $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                } else {
                    $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                }

                $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
                $columnIndex++;
            }

            $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
            $row++;
        }

        foreach($otherPlanPayments as $paymentName => $paymentAmount) {
            $sheet->setCellValue('A' . $row, $paymentName);
            $sheet->getRowDimension($row)->setRowHeight(30);

            $total = 0;
            $columnIndex = 3;
            foreach($periods as $index => $period) {

                $column = $this->getColumnWord($columnIndex);
                if ($index === 0) {
                    $amount = $paymentAmount;
                } else {
                    $amount = 0;
                }
                $total += $amount;

                $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
                $columnIndex++;
            }

            $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
            $row++;
        }

        $sheet->getRowDimension($row)->setRowHeight(5);
        $row++;

        $sheet->setCellValue('A' . $row, 'РАСХОДЫ ИТОГО, в том числе:');
        $sheet->setCellValue('A' . $row + 1, '     Работы');
        $sheet->setCellValue('A' . $row + 2, '     Материалы');
        $sheet->setCellValue('A' . $row + 3, '     Накладные/Услуги');

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
        $sheet->getRowDimension($row)->setRowHeight(30);
        $sheet->getRowDimension($row + 1)->setRowHeight(25);
        $sheet->getRowDimension($row + 2)->setRowHeight(25);
        $sheet->getRowDimension($row + 3)->setRowHeight(25);

        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('e7e7e7');
        $sheet->getStyle('A' . $row . ':'. $lastColumn . ($row + 3))->getFont()->setItalic(true);
        $sheet->getStyle('A' . $row . ':'. $lastColumn . ($row + 3))->getFont()->setSize(11);

        $total = 0;
        $totalContractors = 0;
        $totalProviders = 0;
        $totalService = 0;
        $columnIndex = 3;
        foreach($periods as $period) {
            $column = $this->getColumnWord($columnIndex);

            $amount = $cfPayments['total']['all'][$period['start']];
            $contractors = $cfPayments['total']['contractors'][$period['start']];
            $providers = $cfPayments['total']['providers_fix'][$period['start']] + $cfPayments['total']['providers_float'][$period['start']];
            $service = $cfPayments['total']['service'][$period['start']];

            $total += $amount;
            $totalContractors += $contractors;
            $totalProviders += $providers;
            $totalService += $service;

            $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
            $sheet->setCellValue($column . ($row + 1), $contractors != 0 ? $contractors : '');
            $sheet->setCellValue($column . ($row + 2), $providers != 0 ? $providers : '');
            $sheet->setCellValue($column . ($row + 3), $service != 0 ? $service : '');

            $columnIndex++;
        }

        $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
        $sheet->setCellValue($lastColumn . ($row + 1), $totalContractors != 0 ? $totalContractors : '');
        $sheet->setCellValue($lastColumn . ($row + 2), $totalProviders != 0 ? $totalProviders : '');
        $sheet->setCellValue($lastColumn . ($row + 3), $totalService != 0 ? $totalService : '');

        $row = $row + 4;

        foreach($cfPayments['objects'] as $objectId => $payment) {

            $object = BObject::find($objectId);

            $sheet->setCellValue('A' . $row, $object->name);
            $sheet->setCellValue('B' . $row, $object->code);
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

            $columnIndex = 3;
            $total = 0;
            foreach($periods as $period) {
                $column = $this->getColumnWord($columnIndex);

                $amount = $cfPayments['objects'][$object->id][$period['start']]['total'] ?? 0;
                $total += $amount;

                $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');

                $columnIndex++;
            }
            $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
            $row++;

            if ($object->code === '363') {
                $sheet->setCellValue('A' . $row, '    ' . 'Работы');
                $sheet->setCellValue('A' . $row + 1, '    ' . 'Материалы');
                $sheet->setCellValue('A' . $row + 2, '        ' . '- фиксированная часть');
                $sheet->setCellValue('A' . $row + 3, '        ' . '- изменяемая часть');
                $sheet->setCellValue('A' . $row + 4, '    ' . 'Накладные/Услуги');

                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->getRowDimension($row + 1)->setRowHeight(25);
                $sheet->getRowDimension($row + 2)->setRowHeight(25);
                $sheet->getRowDimension($row + 3)->setRowHeight(25);
                $sheet->getRowDimension($row + 4)->setRowHeight(25);

                $sheet->getStyle('A' . $row . ':' . $lastColumn . ($row + 4))->getFont()->setItalic(true);
                $sheet->getStyle('A' . $row . ':' . $lastColumn . ($row + 4))->getFont()->setSize(11);
            } else {
                $sheet->setCellValue('A' . $row, '    ' . 'Работы');
                $sheet->setCellValue('A' . $row + 1, '    ' . 'Материалы');
                $sheet->setCellValue('A' . $row + 2, '    ' . 'Накладные/Услуги');

                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->getRowDimension($row + 1)->setRowHeight(25);
                $sheet->getRowDimension($row + 2)->setRowHeight(25);

                $sheet->getStyle('A' . $row . ':' . $lastColumn . ($row + 2))->getFont()->setItalic(true);
                $sheet->getStyle('A' . $row . ':' . $lastColumn . ($row + 2))->getFont()->setSize(11);
            }

            if ($object->code === '363') {
                $columnIndex = 3;
                $totalContractors = 0;
                $totalProviders = 0;
                $totalProvidersFix = 0;
                $totalProvidersFloat = 0;
                $totalService = 0;
                foreach($periods as $period) {
                    $column = $this->getColumnWord($columnIndex);

                    $contractors = $cfPayments['objects'][$object->id][$period['start']]['contractors'] ?? 0;
                    $providers = ($cfPayments['objects'][$object->id][$period['start']]['providers_fix'] ?? 0) + ($cfPayments['objects'][$object->id][$period['start']]['providers_float'] ?? 0);
                    $providersFix = $cfPayments['objects'][$object->id][$period['start']]['providers_fix'] ?? 0;
                    $providersFloat = $cfPayments['objects'][$object->id][$period['start']]['providers_float'] ?? 0;
                    $service = $cfPayments['objects'][$object->id][$period['start']]['service'] ?? 0;

                    $totalContractors += $contractors;
                    $totalProviders += $providers;
                    $totalProvidersFix += $providersFix;
                    $totalProvidersFloat += $providersFloat;
                    $totalService += $service;

                    $sheet->setCellValue($column . $row, $contractors != 0 ? $contractors : '');
                    $sheet->setCellValue($column . ($row + 1), $providers != 0 ? $providers : '');
                    $sheet->setCellValue($column . ($row + 2), $providersFix != 0 ? $providersFix : '');
                    $sheet->setCellValue($column . ($row + 3), $providersFloat != 0 ? $providersFloat : '');
                    $sheet->setCellValue($column . ($row + 4), $service != 0 ? $service : '');

                    $columnIndex++;
                }

                $sheet->setCellValue($lastColumn . $row, $totalContractors != 0 ? $totalContractors : '');
                $sheet->setCellValue($lastColumn . ($row + 1), $totalProviders != 0 ? $totalProviders : '');
                $sheet->setCellValue($lastColumn . ($row + 2), $totalProvidersFix != 0 ? $totalProvidersFix : '');
                $sheet->setCellValue($lastColumn . ($row + 3), $totalProvidersFloat != 0 ? $totalProvidersFloat : '');
                $sheet->setCellValue($lastColumn . ($row + 4), $totalService != 0 ? $totalService : '');

                $row = $row + 5;
            } else {
                $columnIndex = 3;
                $totalContractors = 0;
                $totalProviders = 0;
                $totalService = 0;
                foreach($periods as $period) {
                    $column = $this->getColumnWord($columnIndex);

                    $contractors = $cfPayments['objects'][$object->id][$period['start']]['contractors'] ?? 0;
                    $providers = ($cfPayments['objects'][$object->id][$period['start']]['providers_fix'] ?? 0) + ($cfPayments['objects'][$object->id][$period['start']]['providers_float'] ?? 0);
                    $service = $cfPayments['objects'][$object->id][$period['start']]['service'] ?? 0;

                    $totalContractors += $contractors;
                    $totalProviders += $providers;
                    $totalService += $service;

                    $sheet->setCellValue($column . $row, $contractors != 0 ? $contractors : '');
                    $sheet->setCellValue($column . ($row + 1), $providers != 0 ? $providers : '');
                    $sheet->setCellValue($column . ($row + 2), $service != 0 ? $service : '');

                    $columnIndex++;
                }

                $sheet->setCellValue($lastColumn . $row, $totalContractors != 0 ? $totalContractors : '');
                $sheet->setCellValue($lastColumn . ($row + 1), $totalProviders != 0 ? $totalProviders : '');
                $sheet->setCellValue($lastColumn . ($row + 2), $totalService != 0 ? $totalService : '');

                $row = $row + 3;
            }
        }

        $sheet->getRowDimension($row)->setRowHeight(5);
        $row++;

        $sheet->setCellValue('A' . $row, 'Итого расходов по неделям:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $total = 0;
        $columnIndex = 3;
        foreach($periods as $index => $period) {

            $column = $this->getColumnWord($columnIndex);
            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') - $cfPayments['total']['all'][$period['start']] + array_sum($otherPlanPayments);
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') - $cfPayments['total']['all'][$period['start']];
            }

            $total += $amount;

            $sheet->setCellValue($column . $row, $amount != 0 ? $amount : '');
            $columnIndex++;
        }

        $sheet->setCellValue($lastColumn . $row, $total != 0 ? $total : '');
        $row++;

        $sheet->setCellValue('A' . $row, 'Итого расходов по месяцам:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $row++;
        $sheet->setCellValue('A' . $row, 'Сальдо (без учета целевых авансов) по неделям:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $columnIndex = 3;
        foreach($periods as $index => $period) {

            $column = $this->getColumnWord($columnIndex);
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments) + array_sum($accounts) - $cfPayments['total']['all'][$period['start']];
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') - $cfPayments['total']['all'][$period['start']];
            }

            $diff = $otherAmount - $amount;

            $sheet->setCellValue($column . $row, $diff != 0 ? $diff : '');
            $sheet->getStyle($column . $row)->getFont()->setColor(new Color($diff < 0 ? Color::COLOR_RED : Color::COLOR_BLACK));

            $columnIndex++;
        }

        $row++;
        $sheet->setCellValue('A' . $row, 'Накопительное Сальдо (без учета целевых авансов) по неделям:');
        $sheet->getRowDimension($row)->setRowHeight(30);

        $prev = 0;
        $columnIndex = 3;
        foreach($periods as $index => $period) {

            $column = $this->getColumnWord($columnIndex);
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments) + array_sum($accounts) - $cfPayments['total']['all'][$period['start']];
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') - $cfPayments['total']['all'][$period['start']];
            }

            $diff = $otherAmount - $amount + $prev;
            $prev = $diff;

            $sheet->setCellValue($column . $row, $diff != 0 ? $diff : '');
            $sheet->getStyle($column . $row)->getFont()->setColor(new Color($diff < 0 ? Color::COLOR_RED : Color::COLOR_DARKGREEN));

            $columnIndex++;
        }


        $sheet->getStyle('A1:' . 'A' . $row)->getAlignment()->setVertical('center')->setHorizontal('left');
        $sheet->getStyle('C2:' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('right');
        $sheet->getStyle('B' . ($row - 1) . ':' . $lastColumn . $row)->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('B1:B' . $row)->getAlignment()->setVertical('center')->setHorizontal('center');

        $sheet->getStyle('A' . ($row - 3) . ':' . $lastColumn . $row)->getFont()->setBold(true);

        $sheet->getStyle('C2:' . $lastColumn . $row)->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getStyle('A' . ($row - 3) . ':' . $lastColumn . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('f7f7f7');

        $sheet->getStyle('A1:' . $lastColumn . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'c7c7c7']]]
        ]);
    }

    private function getColumnWord($n) {
        $n--;
        return ($n<26) ? chr(ord('A') + $n) : 'A' .  chr(ord('A') + $n % 26);
    }
}
