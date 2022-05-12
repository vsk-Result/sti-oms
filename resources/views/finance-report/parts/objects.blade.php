@inject('contractService', 'App\Services\Contract\ContractService')

@php
    $total = [];
    $paymentQuery = \App\Models\Payment::select('object_id', 'amount');
    $objects = \App\Models\Object\BObject::whereIn('code', ['288', '317', '325', '332', '338', '342', '343', '344', '346', '349', '352', '353', '354', '357', '358'])->get();

    foreach ($objects as $object) {
        $total[$object->code]['payment_total_pay'] = (clone $paymentQuery)->where('object_id', $object->id)->where('amount', '<', 0)->sum('amount');
        $total[$object->code]['payment_total_receive'] = (clone $paymentQuery)->where('object_id', $object->id)->sum('amount') - $object->total_pay;
        $total[$object->code]['payment_total_balance'] = $total[$object->code]['payment_total_pay'] + $total[$object->code]['payment_total_receive'];

        $totalInfo = [];
        $contracts = $contractService->filterContracts(['object_id' => [$object->id]], $totalInfo);

        $total[$object->code]['contract_total_amount']['RUB'] = $totalInfo['amount']['RUB'];
        $total[$object->code]['contract_total_amount']['EUR'] = $totalInfo['amount']['EUR'];

        $total[$object->code]['contract_avanses_non_closes_amount']['RUB'] = $totalInfo['avanses_non_closes_amount']['RUB'];
        $total[$object->code]['contract_avanses_non_closes_amount']['EUR'] = $totalInfo['avanses_non_closes_amount']['EUR'];

        $total[$object->code]['contract_avanses_left_amount']['RUB'] = $totalInfo['avanses_left_amount']['RUB'];
        $total[$object->code]['contract_avanses_left_amount']['EUR'] = $totalInfo['avanses_left_amount']['EUR'];

        $total[$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] = $totalInfo['avanses_acts_left_paid_amount']['RUB'];
        $total[$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] = $totalInfo['avanses_acts_left_paid_amount']['EUR'];

        $total[$object->code]['contract_avanses_received_amount']['RUB'] = $totalInfo['avanses_received_amount']['RUB'];
        $total[$object->code]['contract_avanses_received_amount']['EUR'] = $totalInfo['avanses_received_amount']['EUR'];

        $total[$object->code]['contract_avanses_acts_paid_amount']['RUB'] = $totalInfo['avanses_acts_paid_amount']['RUB'];
        $total[$object->code]['contract_avanses_acts_paid_amount']['EUR'] = $totalInfo['avanses_acts_paid_amount']['EUR'];

        $total[$object->code]['contract_avanses_notwork_left_amount']['RUB'] = $totalInfo['avanses_notwork_left_amount']['RUB'];
        $total[$object->code]['contract_avanses_notwork_left_amount']['EUR'] = $totalInfo['avanses_notwork_left_amount']['EUR'];

        $total[$object->code]['contract_avanses_acts_deposites_amount']['RUB'] = $totalInfo['avanses_acts_deposites_amount']['RUB'];
        $total[$object->code]['contract_avanses_acts_deposites_amount']['EUR'] = $totalInfo['avanses_acts_deposites_amount']['EUR'];
    }
@endphp

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">

        </div>
        <div class="card-toolbar">

        </div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <table class="table table-hover align-middle table-row-dashed fs-7 gy-5" id="kt_table_users">
            <thead>
            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                <th class="min-w-200px">Объект</th>
                <th class="min-w-150px">Текущий баланс</th>
                <th class="min-w-150px">Общая сумма договоров</th>
                <th class="min-w-150px">Остаток денег к получ. с учётом ГУ</th>
                <th class="min-w-150px">Сумма аванса к получению</th>
                <th class="min-w-150px">Долг подписанных актов</th>
                <th class="min-w-150px">Всего оплачено авансов</th>
                <th class="min-w-150px">Всего оплачено по актам</th>
                <th class="min-w-150px">Не закрытый аванс</th>
                <th class="min-w-150px">Долг гарантийного удержания</th>
                <th class="min-w-150px">Долг подрядчикам</th>
                <th class="min-w-150px">Долг за материалы</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
                @foreach($objects as $object)
                    <tr>
                        <td>{{ $object->getName() }}</td>
                        <td>
                            <span class="{{ $total[$object->code]['payment_total_balance'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['payment_total_balance'], 'RUB') }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_total_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_total_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_total_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_total_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_avanses_non_closes_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_non_closes_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_avanses_non_closes_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_non_closes_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_avanses_left_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_left_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_avanses_left_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_left_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_avanses_acts_left_paid_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_acts_left_paid_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_avanses_acts_left_paid_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_acts_left_paid_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_avanses_received_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_received_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_avanses_received_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_received_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_avanses_acts_paid_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_acts_paid_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_avanses_acts_paid_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_acts_paid_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_avanses_notwork_left_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_notwork_left_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_avanses_notwork_left_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_notwork_left_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="{{ $total[$object->code]['contract_avanses_acts_deposites_amount']['RUB'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_acts_deposites_amount']['RUB'], 'RUB', 0, true) }}
                            </span>
                            <br />
                            <span class="{{ $total[$object->code]['contract_avanses_acts_deposites_amount']['EUR'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ \App\Models\CurrencyExchangeRate::format($total[$object->code]['contract_avanses_acts_deposites_amount']['EUR'], 'EUR', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-danger">
                                {{ \App\Models\CurrencyExchangeRate::format($object->getContractorDebtsAmount(), 'RUB', 0, true) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-danger">
                                {{ \App\Models\CurrencyExchangeRate::format($object->getProviderDebtsAmount(), 'RUB', 0, true) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
