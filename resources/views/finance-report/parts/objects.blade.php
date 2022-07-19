@inject('contractService', 'App\Services\Contract\ContractService')

@php
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
    ];
    $paymentQuery = \App\Models\Payment::select('object_id', 'amount');
    $objects = \App\Models\Object\BObject::whereIn('code', ['288', '317', '325', '332', '338', '342', '343', '344', '346', '349', '352', '353', '354', '357', '358', '359'])->get();

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
        $contracts = $contractService->filterContracts(['object_id' => [$object->id]], $totalInfo);

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

        $ITRSalaryObject = \App\Models\CRM\ItrSalary::where('kod', 'LIKE', '%' . $object->code. '%')->get();

        $total[$object->code]['salary_itr']['RUB'] = $ITRSalaryObject->sum('paid') - $ITRSalaryObject->sum('total');
        $summary['contractor']['RUB'] += $total[$object->code]['contractor']['RUB'];
        $summary['provider']['RUB'] += $total[$object->code]['provider']['RUB'];
        $summary['salary_itr']['RUB'] += $total[$object->code]['salary_itr']['RUB'];
    }

    $infos = [
        'Текущий баланс' => 'payment_total_balance',
        'Общие затраты' => 'general_costs_amount',
        'Общая сумма договоров' => 'contract_total_amount',
        'Остаток денег к получ. с учётом ГУ' => 'contract_avanses_non_closes_amount',
        'Сумма аванса к получению' => 'contract_avanses_left_amount',
        'Долг подписанных актов' => 'contract_avanses_acts_left_paid_amount',
        'Всего оплачено авансов' => 'contract_avanses_received_amount',
        'Всего оплачено по актам' => 'contract_avanses_acts_paid_amount',
        'Не закрытый аванс' => 'contract_avanses_notwork_left_amount',
        'Долг гарантийного удержания' => 'contract_avanses_acts_deposites_amount',
        'Долг подрядчикам' => 'contractor',
        'Долг за материалы' => 'provider',
      //  'Долг на зарплаты ИТР' => 'salary_itr',
    ];
@endphp

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">

        </div>
        <div class="card-toolbar">

        </div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <table class="objects-table table table-hover align-middle table-row-dashed fs-7 gy-5" id="kt_table_users">
            <thead>
            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                <th class="min-w-200px br ps-2">Сводка</th>
                <th class="min-w-200px hl">Итого</th>
                @foreach($objects as $object)
                    <th class="min-w-150px col-object {{ $loop->first ? 'bl' : '' }}">{{ $object->getName() }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach($infos as $info => $field)
                    <tr>
                        <td class="br ps-2">{{ $info }}</td>
                        <td class="fw-bolder hl text-right">
                            @if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24')
                                @continue
                            @endif

                            @if(in_array($field, ['payment_total_balance', 'general_costs_amount']))
                                <span class="{{ $summary[$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB') }}
                                </span>
                            @elseif($field === 'contractor' || $field === 'provider')
                                <span class="text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB', 0, true) }}
                                </span>
                            @elseif($field === 'salary_itr')
                                <span class="{{ $summary[$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB', 0, true) }}
                                </span>
                            @else
                                <span class="{{ $summary[$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($summary[$field]['RUB'], 'RUB', 0, true) }}
                                </span>
                                <br />
                                <span class="{{ $summary[$field]['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ \App\Models\CurrencyExchangeRate::format($summary[$field]['EUR'], 'EUR', 0, true) }}
                                </span>
                            @endif
                        </td>
                        @foreach($objects as $object)
                            <td class="text-right {{ $loop->first ? 'bl' : '' }} {{ $loop->last ? 'pe-4' : '' }}">
                                @if(in_array($field, ['payment_total_balance', 'general_costs_amount', 'general_costs_amount_1', 'general_costs_amount_24']))
                                    @if ($object->code == 288)
                                        @if ($field === 'general_costs_amount')
                                            1: <span class="{{ $total[$object->code]['general_costs_amount_1'] < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['general_costs_amount_1'], 'RUB') }}
                                            </span>
                                            <br />
                                            2+4: <span class="{{ $total[$object->code]['general_costs_amount_24'] < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['general_costs_amount_24'], 'RUB') }}
                                            </span>
                                            @continue
                                        @endif
                                        @if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24')
                                            @continue
                                        @else
                                            <span class="{{ $total[$object->code][$field] < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code][$field], 'RUB') }}
                                            </span>
                                        @endif
                                    @else
                                        @if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24')
                                            @continue
                                        @endif

                                        <span class="{{ $total[$object->code][$field] < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($total[$object->code][$field], 'RUB') }}
                                        </span>
                                    @endif
                                @elseif($field === 'contractor' || $field === 'provider')
                                    <span class="text-danger">
                                        {{ \App\Models\CurrencyExchangeRate::format($total[$object->code][$field]['RUB'], 'RUB', 0, true) }}
                                    </span>
                                @elseif($field === 'salary_itr')
                                    <span class="{{ $total[$object->code][$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($total[$object->code][$field]['RUB'], 'RUB', 0, true) }}
                                    </span>
                                @else
                                    <span class="{{ $total[$object->code][$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($total[$object->code][$field]['RUB'], 'RUB', 0, true) }}
                                    </span>
                                    <br />
                                    <span class="{{ $total[$object->code][$field]['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($total[$object->code][$field]['EUR'], 'EUR', 0, true) }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
