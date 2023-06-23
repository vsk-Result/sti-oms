@php
    $total = $objectsInfo->total;
    $years = array_reverse(collect($objectsInfo->years)->toArray(), true);
    $objects = $objectsInfo->objects;
    $summary = $objectsInfo->summary;
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
                                    <th class="min-w-150px col-object {{ $loop->first ? 'bl' : '' }}">{{ $object->code . ' | '  . $object->name }}</th>
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
                                            <span class="{{ $summary->{$year}->{$field}->{'RUB'} < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary->{$year}->{$field}->{'RUB'}, 'RUB') }}
                                    </span>
                                        @elseif($field === 'contractor' || $field === 'provider')
                                            <span class="text-danger">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary->{$year}->{$field}->{'RUB'}, 'RUB', 0, true) }}
                                    </span>
                                        @elseif($field === 'salary_itr' || $field === 'salary_work')
                                            <span class="{{ $summary->{$year}->{$field}->{'RUB'} < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary->{$year}->{$field}->{'RUB'}, 'RUB', 0, true) }}
                                    </span>
                                        @else
                                            <span class="{{ $summary->{$year}->{$field}->{'RUB'} < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary->{$year}->{$field}->{'RUB'}, 'RUB', 0, true) }}
                                    </span>
                                            <br />
                                            <span class="{{ $summary->{$year}->{$field}->{'EUR'} < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ \App\Models\CurrencyExchangeRate::format($summary->{$year}->{$field}->{'EUR'}, 'EUR', 0, true) }}
                                    </span>
                                        @endif
                                    </td>
                                    @foreach($objects as $object)
                                        <td class="text-right {{ $loop->first ? 'bl' : '' }} {{ $loop->last ? 'pe-4' : '' }}">
                                            @if(in_array($field, ['payment_total_balance', 'general_costs_amount', 'general_costs_amount_1', 'general_costs_amount_24']))
                                                @if ($object->code == 288)
                                                    @if ($field === 'general_costs_amount')
                                                        1: <span class="{{ $total->{$year}->{$object->code}->{'general_costs_amount_1'} < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{'general_costs_amount_1'}, 'RUB') }}
                                                </span>
                                                        <br />
                                                        2+4: <span class="{{ $total->{$year}->{$object->code}->{'general_costs_amount_24'} < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{'general_costs_amount_24'}, 'RUB') }}
                                                </span>
                                                        @continue
                                                    @endif
                                                    @if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24')
                                                        @continue
                                                    @else
                                                        <span class="{{ $total->{$year}->{$object->code}->{$field} < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{$field}, 'RUB') }}
                                                </span>
                                                    @endif
                                                @else
                                                    @if ($field === 'general_costs_amount_1' || $field === 'general_costs_amount_24')
                                                        @continue
                                                    @endif

                                                    <span class="{{ $total->{$year}->{$object->code}->{$field} < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{$field}, 'RUB') }}
                                            </span>
                                                @endif
                                            @elseif($field === 'contractor' || $field === 'provider')
                                                <span class="text-danger">
                                            {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{$field}->{'RUB'}, 'RUB', 0, true) }}
                                        </span>
                                            @elseif($field === 'salary_itr' || $field === 'salary_work')
                                                <span class="{{ $total->{$year}->{$object->code}->{$field}->{'RUB'} < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{$field}->{'RUB'}, 'RUB', 0, true) }}
                                        </span>
                                            @else
                                                <span class="{{ $total->{$year}->{$object->code}->{$field}->{'RUB'} < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{$field}->{'RUB'}, 'RUB', 0, true) }}
                                        </span>
                                                <br />
                                                <span class="{{ $total->{$year}->{$object->code}->{$field}->{'EUR'} < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{$field}->{'EUR'}, 'EUR', 0, true) }}
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
