<?php

namespace App\Exports\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Sheets\ObjectPivotSheet;
use App\Exports\Finance\FinanceReport\Sheets\PivotSheet;
use App\Models\Payment;
use App\Models\Status;
use App\Services\Contract\ContractService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $info;

    private ContractService $contractService;

    public function __construct(array $info, ContractService $contractService)
    {
        $this->info = $info;
        $this->contractService = $contractService;
    }

    public function sheets(): array
    {
        $objects = \App\Models\Object\BObject::withoutGeneral()
            ->orderByDesc('code')
            ->orderByDesc('closing_date')
            ->get();

        $years = [];

        foreach ($objects as $object) {
            if (($object->status_id === Status::STATUS_BLOCKED) && ! empty($object->closing_date)) {
                $year = Carbon::parse($object->closing_date)->format('Y');

                $years[$year][] = $object;
            } else if ($object->status_id === Status::STATUS_BLOCKED && empty($object->closing_date)) {
                $years['Закрыты, дата не указана'][] = $object;
            } else {
                $years['Активные'][] = $object;
            }
        }

        $sheets = [
            new PivotSheet('Сводная по балансам и долгам', $this->info),
        ];

        foreach ($years as $year => $objects) {
            $sheetName = 'Сводная (' . $year . ')';
            $pivotInfo = [
                'pivot_list' => [
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
                ],
                'pivot_info' => $this->getYearPivotInformation($objects),
                'objects' => $objects
            ];

            $sheets[] = new ObjectPivotSheet($sheetName, $pivotInfo);
        }

//        $objects = BObject::active()
//            ->orderByDesc('code')
//            ->get();
//
//        foreach ($objects as $object) {
//            $sheets[] = new ObjectSheet($object->getName(), $this->contractService, $object);
//        }

        return $sheets;
    }

    private function getYearPivotInformation(array $objects): array
    {
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

        $total = [];
        $paymentQuery = Payment::select('object_id', 'amount');

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

            $ITRSalaryObject = \App\Models\CRM\ItrSalary::where('kod', 'LIKE', '%' . $object->code . '%')->get();
            $workSalaryObjectAmount = \App\Models\CRM\SalaryDebt::where('object_code', 'LIKE', '%' . $object->code . '%')->sum('amount');

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

        return ['total' => $total, 'summary' => $summary];
    }
}
