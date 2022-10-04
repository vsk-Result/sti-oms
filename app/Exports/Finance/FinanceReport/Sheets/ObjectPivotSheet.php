<?php

namespace App\Exports\Finance\FinanceReport\Sheets;

use App\Models\CRM\ItrSalary;
use App\Models\CRM\SalaryDebt;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Models\Status;
use App\Services\Contract\ContractService;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ObjectPivotSheet implements
    WithTitle,
    WithStyles
{
    private string $sheetName;

    private ContractService $contractService;

    public function __construct(string $sheetName, ContractService $contractService)
    {
        $this->sheetName = $sheetName;
        $this->contractService = $contractService;
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
        $total = [];
        $summary = [
            'payment_total_balance' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'general_costs_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'general_costs_with_balance_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_total_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_avanses_non_closes_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_avanses_left_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_avanses_acts_left_paid_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_avanses_received_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_avanses_acts_paid_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_avanses_notwork_left_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contract_avanses_acts_deposites_amount' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'contractor' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'provider' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'salary_itr' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'salary_work' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'interim_balance' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
            'interim_balance_non_closes' => [
                'RUB' => 0,
                'EUR' => 0,
            ],
        ];
        $paymentQuery = Payment::select('object_id', 'amount');
        $objects = BObject::active()
            ->orderByDesc('code')
            ->get();

        foreach ($objects as $object) {
            $total[$object->code]['payment_total_pay'] = (clone $paymentQuery)->where('object_id', $object->id)->where('amount', '<', 0)->sum('amount');
            $total[$object->code]['payment_total_receive'] = (clone $paymentQuery)->where('object_id', $object->id)->sum('amount') - $total[$object->code]['payment_total_pay'];
            $total[$object->code]['payment_total_balance'] = $total[$object->code]['payment_total_pay'] + $total[$object->code]['payment_total_receive'];
            if ($object->code == 288) {
                $total[$object->code]['general_costs_amount_1'] = $object->generalCosts()->where('is_pinned', false)->sum('amount');
                $total[$object->code]['general_costs_amount_24'] = $object->generalCosts()->where('is_pinned', true)->sum('amount');
            }

            $total[$object->code]['general_costs_amount'] = $object->generalCosts()->sum('amount');
            // $total[$object->code]['general_costs_with_balance_amount'] = $total[$object->code]['payment_total_balance'] +  $total[$object->code]['general_costs_amount'];


            $summary['payment_total_balance']['RUB'] += $total[$object->code]['payment_total_balance'];
            $summary['general_costs_amount']['RUB'] += $total[$object->code]['general_costs_amount'];
            // $summary['general_costs_with_balance_amount']['RUB'] += $total[$object->code]['general_costs_with_balance_amount'];

            $totalInfo = [];
            $contracts = $this->contractService->filterContracts(['object_id' => [$object->id]], $totalInfo);

            $total[$object->code]['contract_total_amount']['RUB'] = $totalInfo['amount']['RUB'];
            $total[$object->code]['contract_total_amount']['EUR'] = $totalInfo['amount']['EUR'];
            $summary['contract_total_amount']['RUB'] += $total[$object->code]['contract_total_amount']['RUB'];
            $summary['contract_total_amount']['EUR'] += $total[$object->code]['contract_total_amount']['EUR'];

            $total[$object->code]['contract_avanses_non_closes_amount']['RUB'] = $totalInfo['avanses_non_closes_amount']['RUB'];
            $total[$object->code]['contract_avanses_non_closes_amount']['EUR'] = $totalInfo['avanses_non_closes_amount']['EUR'];
            $summary['contract_avanses_non_closes_amount']['RUB'] += $total[$object->code]['contract_avanses_non_closes_amount']['RUB'];
            $summary['contract_avanses_non_closes_amount']['EUR'] += $total[$object->code]['contract_avanses_non_closes_amount']['EUR'];

            $total[$object->code]['contract_avanses_left_amount']['RUB'] = $totalInfo['avanses_left_amount']['RUB'];
            $total[$object->code]['contract_avanses_left_amount']['EUR'] = $totalInfo['avanses_left_amount']['EUR'];
            $summary['contract_avanses_left_amount']['RUB'] += $total[$object->code]['contract_avanses_left_amount']['RUB'];
            $summary['contract_avanses_left_amount']['EUR'] += $total[$object->code]['contract_avanses_left_amount']['EUR'];

            $total[$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] = $totalInfo['avanses_acts_left_paid_amount']['RUB'];
            $total[$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] = $totalInfo['avanses_acts_left_paid_amount']['EUR'];
            $summary['contract_avanses_acts_left_paid_amount']['RUB'] += $total[$object->code]['contract_avanses_acts_left_paid_amount']['RUB'];
            $summary['contract_avanses_acts_left_paid_amount']['EUR'] += $total[$object->code]['contract_avanses_acts_left_paid_amount']['EUR'];

            $total[$object->code]['contract_avanses_received_amount']['RUB'] = $totalInfo['avanses_received_amount']['RUB'];
            $total[$object->code]['contract_avanses_received_amount']['EUR'] = $totalInfo['avanses_received_amount']['EUR'];
            $summary['contract_avanses_received_amount']['RUB'] += $total[$object->code]['contract_avanses_received_amount']['RUB'];
            $summary['contract_avanses_received_amount']['EUR'] += $total[$object->code]['contract_avanses_received_amount']['EUR'];

            $total[$object->code]['contract_avanses_acts_paid_amount']['RUB'] = $totalInfo['avanses_acts_paid_amount']['RUB'];
            $total[$object->code]['contract_avanses_acts_paid_amount']['EUR'] = $totalInfo['avanses_acts_paid_amount']['EUR'];
            $summary['contract_avanses_acts_paid_amount']['RUB'] += $total[$object->code]['contract_avanses_acts_paid_amount']['RUB'];
            $summary['contract_avanses_acts_paid_amount']['EUR'] += $total[$object->code]['contract_avanses_acts_paid_amount']['EUR'];

            $total[$object->code]['contract_avanses_notwork_left_amount']['RUB'] = $totalInfo['avanses_notwork_left_amount']['RUB'];
            $total[$object->code]['contract_avanses_notwork_left_amount']['EUR'] = $totalInfo['avanses_notwork_left_amount']['EUR'];
            $summary['contract_avanses_notwork_left_amount']['RUB'] += $total[$object->code]['contract_avanses_notwork_left_amount']['RUB'];
            $summary['contract_avanses_notwork_left_amount']['EUR'] += $total[$object->code]['contract_avanses_notwork_left_amount']['EUR'];

            $total[$object->code]['contract_avanses_acts_deposites_amount']['RUB'] = $totalInfo['avanses_acts_deposites_amount']['RUB'];
            $total[$object->code]['contract_avanses_acts_deposites_amount']['EUR'] = $totalInfo['avanses_acts_deposites_amount']['EUR'];
            $summary['contract_avanses_acts_deposites_amount']['RUB'] += $total[$object->code]['contract_avanses_acts_deposites_amount']['RUB'];
            $summary['contract_avanses_acts_deposites_amount']['EUR'] += $total[$object->code]['contract_avanses_acts_deposites_amount']['EUR'];

            $total[$object->code]['contractor']['RUB'] = $object->getContractorDebtsAmount();
            $total[$object->code]['provider']['RUB'] = $object->getProviderDebtsAmount();

            $ITRSalaryObject = ItrSalary::where('kod', 'LIKE', '%' . $object->code. '%')->get();
            $workSalaryObjectAmount = SalaryDebt::where('object_code', 'LIKE', '%' . $object->code. '%')->sum('amount');

            $total[$object->code]['salary_itr']['RUB'] = $ITRSalaryObject->sum('paid') - $ITRSalaryObject->sum('total');
            $total[$object->code]['salary_work']['RUB'] = $workSalaryObjectAmount;

            $total[$object->code]['interim_balance']['RUB'] =
                $total[$object->code]['payment_total_balance'] +
                $total[$object->code]['general_costs_amount'] +
                $total[$object->code]['contract_avanses_left_amount']['RUB'] +
                $total[$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] +
                $total[$object->code]['contract_avanses_acts_deposites_amount']['RUB'] +
                $total[$object->code]['contractor']['RUB'] +
                $total[$object->code]['provider']['RUB'] +
                $total[$object->code]['salary_itr']['RUB'] +
                $total[$object->code]['salary_work']['RUB'];

            $total[$object->code]['interim_balance']['EUR'] =
                $total[$object->code]['contract_avanses_left_amount']['EUR'] +
                $total[$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] +
                $total[$object->code]['contract_avanses_acts_deposites_amount']['EUR'];

            $total[$object->code]['interim_balance_non_closes']['RUB'] =
                $total[$object->code]['interim_balance']['RUB'] +
                $total[$object->code]['contract_avanses_non_closes_amount']['RUB'] -
                $total[$object->code]['contract_avanses_left_amount']['RUB'];

            $total[$object->code]['interim_balance_non_closes']['EUR'] =
                $total[$object->code]['interim_balance']['EUR'] +
                $total[$object->code]['contract_avanses_non_closes_amount']['EUR'] -
                $total[$object->code]['contract_avanses_left_amount']['EUR'];

            $summary['contractor']['RUB'] += $total[$object->code]['contractor']['RUB'];
            $summary['provider']['RUB'] += $total[$object->code]['provider']['RUB'];
            $summary['salary_itr']['RUB'] += $total[$object->code]['salary_itr']['RUB'];
            $summary['salary_work']['RUB'] += $total[$object->code]['salary_work']['RUB'];
            $summary['interim_balance']['RUB'] += $total[$object->code]['interim_balance']['RUB'];
            $summary['interim_balance']['EUR'] += $total[$object->code]['interim_balance']['EUR'];
            $summary['interim_balance_non_closes']['RUB'] += $total[$object->code]['interim_balance_non_closes']['RUB'];
            $summary['interim_balance_non_closes']['EUR'] += $total[$object->code]['interim_balance_non_closes']['EUR'];
        }

        $infos = [
            'Текущее сальдо' => 'payment_total_balance',
            'Общие затраты' => 'general_costs_amount',
            'Промежуточный баланс с текущими долгами и общими расходами компании' => 'interim_balance',
            'Общая сумма договоров' => 'contract_total_amount',
            'Остаток денег к получ. с учётом ГУ' => 'contract_avanses_non_closes_amount',
            'PROM BALANS +  NE ZAKRITI DOGOVOR' => 'interim_balance_non_closes',
            'Сумма аванса к получению' => 'contract_avanses_left_amount',
            'Долг подписанных актов' => 'contract_avanses_acts_left_paid_amount',
            'Всего оплачено авансов' => 'contract_avanses_received_amount',
            'Всего оплачено по актам' => 'contract_avanses_acts_paid_amount',
            'Не закрытый аванс' => 'contract_avanses_notwork_left_amount',
            'Долг гарантийного удержания' => 'contract_avanses_acts_deposites_amount',
            'Долг подрядчикам' => 'contractor',
            'Долг за материалы' => 'provider',
            'Долг на зарплаты ИТР' => 'salary_itr',
            'Долг на зарплаты рабочим' => 'salary_work',
        ];

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

            $sheet->getRowDimension($row)->setRowHeight(30);
            $row++;
        }

        $row--;

        $lastColumn = $this->getColumnWord(2 + $objects->count());

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
}
