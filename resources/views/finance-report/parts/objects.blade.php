@inject('contractService', 'App\Services\Contract\ContractService')

@php
    $total = [];

    $paymentQuery = \App\Models\Payment::select('object_id', 'amount');
    $objects = \App\Models\Object\BObject::withoutGeneral()
                ->orderByDesc('code')
                ->orderByDesc('closing_date')
                ->get();

    $years = [];

    foreach ($objects as $object) {
        if ($object->status_id === \App\Models\Status::STATUS_BLOCKED && ! empty($object->closing_date)) {
            $year = \Carbon\Carbon::parse($object->closing_date)->format('Y');

            $years[$year][] = $object;
        } else if ($object->status_id === \App\Models\Status::STATUS_BLOCKED && empty($object->closing_date)) {
            $years['Закрыты, дата не указана'][] = $object;
        } else {
            $years['Активные'][] = $object;
        }
    }

    $summary = [];

    foreach ($years as $year => $objects) {
        $summary[$year] = [
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
    }
    foreach ($years as $year => $objects) {
        foreach ($objects as $object) {
            $total[$year][$object->code]['payment_total_pay'] = (clone $paymentQuery)->where('object_id', $object->id)->where('amount', '<', 0)->sum('amount');
            $total[$year][$object->code]['payment_total_receive'] = (clone $paymentQuery)->where('object_id', $object->id)->sum('amount') - $total[$year][$object->code]['payment_total_pay'];
            $total[$year][$object->code]['payment_total_balance'] = $total[$year][$object->code]['payment_total_pay'] + $total[$year][$object->code]['payment_total_receive'];
            if ($object->code == 288) {
                $total[$year][$object->code]['general_costs_amount_1'] = $object->generalCosts()->where('is_pinned', false)->sum('amount');
                $total[$year][$object->code]['general_costs_amount_24'] = $object->generalCosts()->where('is_pinned', true)->sum('amount');
            }

            $total[$year][$object->code]['general_costs_amount'] = $object->generalCosts()->sum('amount');
            // $total[$object->code]['general_costs_with_balance_amount'] = $total[$object->code]['payment_total_balance'] +  $total[$object->code]['general_costs_amount'];


            $summary[$year]['payment_total_balance']['RUB'] += $total[$year][$object->code]['payment_total_balance'];
            $summary[$year]['general_costs_amount']['RUB'] += $total[$year][$object->code]['general_costs_amount'];
            // $summary['general_costs_with_balance_amount']['RUB'] += $total[$object->code]['general_costs_with_balance_amount'];

            $totalInfo = [];
            $contracts = $contractService->filterContracts(['object_id' => [$object->id]], $totalInfo);

            $total[$year][$object->code]['contract_total_amount']['RUB'] = $totalInfo['amount']['RUB'];
            $total[$year][$object->code]['contract_total_amount']['EUR'] = $totalInfo['amount']['EUR'];
            $summary[$year]['contract_total_amount']['RUB'] += $total[$year][$object->code]['contract_total_amount']['RUB'];
            $summary[$year]['contract_total_amount']['EUR'] += $total[$year][$object->code]['contract_total_amount']['EUR'];

            $total[$year][$object->code]['contract_avanses_non_closes_amount']['RUB'] = $totalInfo['avanses_non_closes_amount']['RUB'];
            $total[$year][$object->code]['contract_avanses_non_closes_amount']['EUR'] = $totalInfo['avanses_non_closes_amount']['EUR'];
            $summary[$year]['contract_avanses_non_closes_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_non_closes_amount']['RUB'];
            $summary[$year]['contract_avanses_non_closes_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_non_closes_amount']['EUR'];

            $total[$year][$object->code]['contract_avanses_left_amount']['RUB'] = $totalInfo['avanses_left_amount']['RUB'];
            $total[$year][$object->code]['contract_avanses_left_amount']['EUR'] = $totalInfo['avanses_left_amount']['EUR'];
            $summary[$year]['contract_avanses_left_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_left_amount']['RUB'];
            $summary[$year]['contract_avanses_left_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_left_amount']['EUR'];

            $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] = $totalInfo['avanses_acts_left_paid_amount']['RUB'];
            $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] = $totalInfo['avanses_acts_left_paid_amount']['EUR'];
            $summary[$year]['contract_avanses_acts_left_paid_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['RUB'];
            $summary[$year]['contract_avanses_acts_left_paid_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['EUR'];

            $total[$year][$object->code]['contract_avanses_received_amount']['RUB'] = $totalInfo['avanses_received_amount']['RUB'];
            $total[$year][$object->code]['contract_avanses_received_amount']['EUR'] = $totalInfo['avanses_received_amount']['EUR'];
            $summary[$year]['contract_avanses_received_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_received_amount']['RUB'];
            $summary[$year]['contract_avanses_received_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_received_amount']['EUR'];

            $total[$year][$object->code]['contract_avanses_acts_paid_amount']['RUB'] = $totalInfo['avanses_acts_paid_amount']['RUB'];
            $total[$year][$object->code]['contract_avanses_acts_paid_amount']['EUR'] = $totalInfo['avanses_acts_paid_amount']['EUR'];
            $summary[$year]['contract_avanses_acts_paid_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_acts_paid_amount']['RUB'];
            $summary[$year]['contract_avanses_acts_paid_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_acts_paid_amount']['EUR'];

            $total[$year][$object->code]['contract_avanses_notwork_left_amount']['RUB'] = $totalInfo['avanses_notwork_left_amount']['RUB'];
            $total[$year][$object->code]['contract_avanses_notwork_left_amount']['EUR'] = $totalInfo['avanses_notwork_left_amount']['EUR'];
            $summary[$year]['contract_avanses_notwork_left_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_notwork_left_amount']['RUB'];
            $summary[$year]['contract_avanses_notwork_left_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_notwork_left_amount']['EUR'];

            $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['RUB'] = $totalInfo['avanses_acts_deposites_amount']['RUB'];
            $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['EUR'] = $totalInfo['avanses_acts_deposites_amount']['EUR'];
            $summary[$year]['contract_avanses_acts_deposites_amount']['RUB'] += $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['RUB'];
            $summary[$year]['contract_avanses_acts_deposites_amount']['EUR'] += $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['EUR'];

            $total[$year][$object->code]['contractor']['RUB'] = $object->getContractorDebtsAmount();
            $total[$year][$object->code]['provider']['RUB'] = $object->getProviderDebtsAmount();

            $ITRSalaryObject = \App\Models\CRM\ItrSalary::where('kod', 'LIKE', '%' . $object->code. '%')->get();
            $workSalaryObjectAmount = \App\Models\CRM\SalaryDebt::where('object_code', 'LIKE', '%' . $object->code. '%')->sum('amount');

            $total[$year][$object->code]['salary_itr']['RUB'] = $ITRSalaryObject->sum('paid') - $ITRSalaryObject->sum('total');
            $total[$year][$object->code]['salary_work']['RUB'] = $workSalaryObjectAmount;

            $total[$year][$object->code]['interim_balance']['RUB'] =
                $total[$year][$object->code]['payment_total_balance'] +
                $total[$year][$object->code]['general_costs_amount'] +
                $total[$year][$object->code]['contract_avanses_left_amount']['RUB'] +
                $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] +
                $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['RUB'] +
                $total[$year][$object->code]['contractor']['RUB'] +
                $total[$year][$object->code]['provider']['RUB'] +
                $total[$year][$object->code]['salary_itr']['RUB'] +
                $total[$year][$object->code]['salary_work']['RUB'];

            $total[$year][$object->code]['interim_balance']['EUR'] =
                $total[$year][$object->code]['contract_avanses_left_amount']['EUR'] +
                $total[$year][$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] +
                $total[$year][$object->code]['contract_avanses_acts_deposites_amount']['EUR'];

            $total[$year][$object->code]['interim_balance_non_closes']['RUB'] =
                $total[$year][$object->code]['interim_balance']['RUB'] +
                $total[$year][$object->code]['contract_avanses_non_closes_amount']['RUB'] -
                $total[$year][$object->code]['contract_avanses_left_amount']['RUB'];

            $total[$year][$object->code]['interim_balance_non_closes']['EUR'] =
                $total[$year][$object->code]['interim_balance']['EUR'] +
                $total[$year][$object->code]['contract_avanses_non_closes_amount']['EUR'] -
                $total[$year][$object->code]['contract_avanses_left_amount']['EUR'];

            $summary[$year]['contractor']['RUB'] += $total[$year][$object->code]['contractor']['RUB'];
            $summary[$year]['provider']['RUB'] += $total[$year][$object->code]['provider']['RUB'];
            $summary[$year]['salary_itr']['RUB'] += $total[$year][$object->code]['salary_itr']['RUB'];
            $summary[$year]['salary_work']['RUB'] += $total[$year][$object->code]['salary_work']['RUB'];
            $summary[$year]['interim_balance']['RUB'] += $total[$year][$object->code]['interim_balance']['RUB'];
            $summary[$year]['interim_balance']['EUR'] += $total[$year][$object->code]['interim_balance']['EUR'];
            $summary[$year]['interim_balance_non_closes']['RUB'] += $total[$year][$object->code]['interim_balance_non_closes']['RUB'];
            $summary[$year]['interim_balance_non_closes']['EUR'] += $total[$year][$object->code]['interim_balance_non_closes']['EUR'];
        }
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
@endphp

<div class="card mt-5">
    <div class="card-header position-relative py-0 border-bottom-1 pe-0">
        <h3 class="card-title text-gray-800 fw-bold">Сводная по объектам</h3>
        <ul class="nav nav-tabs nav-stretch flex-nowrap text-nowrap fs-3">
            @foreach($years as $year => $objects)
                <li class="nav-item">
                    <a class="nav-link btn btn-color-gray-600 rounded-bottom-0 btn-active-light btn-active-color-primary {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#FO_year_{{ $year }}">{{ $year }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body pt-0 px-0">
        <div class="tab-content">
            @foreach($years as $year => $objects)
                <div class="tab-pane fade show {{ $loop->first ? 'active' : '' }}" id="FO_year_{{ $year }}" role="tabpanel">
                    <div class="table-responsive freeze-table">
                        <table class="objects-table table table-hover align-middle table-row-dashed fs-7 gy-5">
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
                                            <span class="{{ $summary[$year][$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary[$year][$field]['RUB'], 'RUB') }}
                                    </span>
                                        @elseif($field === 'contractor' || $field === 'provider')
                                            <span class="text-danger">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary[$year][$field]['RUB'], 'RUB', 0, true) }}
                                    </span>
                                        @elseif($field === 'salary_itr' || $field === 'salary_work')
                                            <span class="{{ $summary[$year][$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary[$year][$field]['RUB'], 'RUB', 0, true) }}
                                    </span>
                                        @else
                                            <span class="{{ $summary[$year][$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary[$year][$field]['RUB'], 'RUB', 0, true) }}
                                    </span>
                                            <br />
                                            <span class="{{ $summary[$year][$field]['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary[$year][$field]['EUR'], 'EUR', 0, true) }}
                                    </span>
                                        @endif
                                    </td>
                                    @foreach($objects as $object)
                                        <td class="text-right {{ $loop->first ? 'bl' : '' }} {{ $loop->last ? 'pe-4' : '' }}">
                                            @if(in_array($field, ['payment_total_balance', 'general_costs_amount', 'general_costs_amount_1', 'general_costs_amount_24']))
                                                @if ($object->code == 288)
                                                    @if ($field === 'general_costs_amount')
                                                        1: <span class="{{ $total[$year][$object->code]['general_costs_amount_1'] < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code]['general_costs_amount_1'], 'RUB') }}
                                                </span>
                                                        <br />
                                                        2+4: <span class="{{ $total[$year][$object->code]['general_costs_amount_24'] < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code]['general_costs_amount_24'], 'RUB') }}
                                                </span>
                                                        @continue
                                                    @endif
                                                    @if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24')
                                                        @continue
                                                    @else
                                                        <span class="{{ $total[$year][$object->code][$field] < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code][$field], 'RUB') }}
                                                </span>
                                                    @endif
                                                @else
                                                    @if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24')
                                                        @continue
                                                    @endif

                                                    <span class="{{ $total[$year][$object->code][$field] < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code][$field], 'RUB') }}
                                            </span>
                                                @endif
                                            @elseif($field === 'contractor' || $field === 'provider')
                                                <span class="text-danger">
                                            {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code][$field]['RUB'], 'RUB', 0, true) }}
                                        </span>
                                            @elseif($field === 'salary_itr' || $field === 'salary_work')
                                                <span class="{{ $total[$year][$object->code][$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code][$field]['RUB'], 'RUB', 0, true) }}
                                        </span>
                                            @else
                                                <span class="{{ $total[$year][$object->code][$field]['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code][$field]['RUB'], 'RUB', 0, true) }}
                                        </span>
                                                <br />
                                                <span class="{{ $total[$year][$object->code][$field]['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($total[$year][$object->code][$field]['EUR'], 'EUR', 0, true) }}
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
            @endforeach
        </div>
    </div>
</div>
