@php
    $total = $objectsInfo->total;
    $years = collect($objectsInfo->years)->toArray();
    $summary = $objectsInfo->summary;
    $infos = App\Models\FinanceReport::getInfoFields();

    $specialFields = ['balance_with_general_balance', 'objectBalance', 'prognozBalance'];
    $prognozFields = array_merge(array_values(\App\Models\FinanceReport::getPrognozFields()), ['receive_customer', 'receive_other', 'receive_retro_dtg']);
    $percentField = 'general_balance_to_receive_percentage';
    $percentFields = ['time_percent', 'complete_percent', 'money_percent', 'plan_ready_percent', 'fact_ready_percent', 'deviation_plan_percent'];
    $exceptFields = ['pay_cash', 'pay_non_cash'];

    unset($years['Не отображать']);
    unset($years['Общие']);
    unset($years['Удаленные']);
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
                                    @php
                                        if (in_array($field, $exceptFields)) {
                                            continue;
                                        }
                                        $sumValue = $summary->{$year}->{$field};
                                        $isSpecialField = in_array($field, $specialFields);
                                        $isPrognozField = in_array($field, $prognozFields);
                                    @endphp
                                    <tr>
                                        <td class="br ps-2 {{ $isSpecialField ? 'fw-boldest' : '' }} {{ $isPrognozField ? 'fw-bold fst-italic fs-8' : '' }}">
                                            @if ($field === 'prognoz_general')
                                                {{ 'Общие расходы (' . number_format(abs($summary->{$year}->{'general_balance_to_receive_percentage'}), 2) . '%)' }}
                                            @else
                                                {{ $info }}
                                            @endif
                                        </td>
                                        <td class="fw-bolder hl text-right">
                                            <span class="{{ in_array($field, $percentFields) ? 'fw-boldest' : '' }} {{ $isSpecialField ? $sumValue < 0 ? 'text-danger' : 'text-success' : '' }} {{ $isPrognozField ? 'fw-bold fst-italic fs-8' : '' }}">
                                                @if ($percentField === $field || in_array($field, $percentFields))
                                                    {{ is_valid_amount_in_range($sumValue) ? '-' : number_format($sumValue, 2) . '%' }}
                                                @else
                                                    {{ \App\Models\CurrencyExchangeRate::format($sumValue, 'RUB') }}
                                                @endif
                                            </span>
                                        </td>
                                        @foreach($objects as $object)
                                            @php
                                                if ($percentField === $field) continue;
                                                $value = $total->{$year}->{$object->code}->{$field};
                                            @endphp
                                            <td class="text-right {{ $loop->first ? 'bl' : '' }} {{ $loop->last ? 'pe-4' : '' }}">
                                                <span class="{{ $isSpecialField ? $value < 0 ? 'text-danger' : 'text-success' : '' }} {{ $isPrognozField ? 'fw-bold fst-italic fs-8' : '' }} {{ $field === 'prognoz_total' ? 'fw-boldest' : '' }}">
                                                    @if (in_array($field, $percentFields))
                                                        @if ($field === 'time_percent')
                                                            {{ $value == 0 ? 'Нет данных' : number_format($value, 2) . '%' }}
                                                        @elseif ($field === 'deviation_plan_percent')
                                                            @if ($value < 0)
                                                                <span class="badge badge-light-danger fs-base mt-n3">
                                                                    <i class="fa fa-arrow-down fs-9 text-danger me-2"></i>{{ number_format($value, 2) . '%' }}
                                                                </span>
                                                            @elseif ($value == 0)
                                                                -
                                                            @else
                                                                <span class="badge badge-light-success fs-base">
                                                                    <i class="fa fa-arrow-up fs-9 text-success me-2"></i>{{ number_format($value, 2) . '%' }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            {{ is_valid_amount_in_range($value) ? '-' : number_format($value, 2) . '%' }}
                                                        @endif
                                                    @else
                                                        {{ is_valid_amount_in_range($value) ? '-' : \App\Models\CurrencyExchangeRate::format($value, 'RUB') }}
                                                    @endif
                                                </span>
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
