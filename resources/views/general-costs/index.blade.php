@extends('layouts.app')

@section('toolbar-title', 'Распределение общих затрат')
@section('breadcrumbs', Breadcrumbs::render('general_costs.index'))

@section('content')
    @php
        $general2017 = \App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $general2018 = \App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $general2019 = \App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $general2020 = \App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $general2021_1 = \App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $general2021_2 = \App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $general2022 = \App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $generalTotal = \App\Models\Payment::whereBetween('date', ['2017-01-01', '2022-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount');
        $objectGeneral = [];
        $objectsTotalCuming = [];
        $objectsTotalCuming['2017'] = 0;
        $objectsTotalCuming['2018'] = 0;
        $objectsTotalCuming['2019'] = 0;
        $objectsTotalCuming['2020'] = 0;
        $objectsTotalCuming['2021_1'] = 0;
        $objectsTotalCuming['2021_2'] = 0;
        $objectsTotalCuming['2022'] = 0;
        $objectsTotalCuming['total'] = 0;
    @endphp
    <div class="card mb-5 mb-xl-8 p-0 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-250px bt bl ps-2">Разбивка общих затрат по годам</th>

                            <th class="min-w-125px bt bl">2017</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($general2017, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2018</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($general2018, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2019</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($general2019, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2020</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($general2020, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">с 01.01.2021 по 02.03.21</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($general2021_1, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">с 03.03.2021 по 31.12.21</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($general2021_2, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2022</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($general2022, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">Итого</th>
                            <th class="min-w-75px bt"></th>
                            <th class="min-w-125px text-danger bt br">{{ \App\Models\CurrencyExchangeRate::format($generalTotal, 'RUB') }}</th>
                        </tr>
                        <tr>
                            <th class="min-w-250px bl ps-2">Объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl"></th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px br"></th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($objects as $object)
                            @php
                                $objectGeneral[$object->id]['name'] = $object->getName();
                                $objectGeneral[$object->id]['2017'] = $object->getComingAmountByPeriodFromCustomers('2017-01-01', '2017-12-31');
                                $objectGeneral[$object->id]['2018'] = $object->getComingAmountByPeriodFromCustomers('2018-01-01', '2018-12-31');
                                $objectGeneral[$object->id]['2019'] = $object->getComingAmountByPeriodFromCustomers('2019-01-01', '2019-12-31');
                                $objectGeneral[$object->id]['2020'] = $object->getComingAmountByPeriodFromCustomers('2020-01-01', '2020-12-31');
                                $objectGeneral[$object->id]['2021_1'] = $object->getComingAmountByPeriodFromCustomers('2021-01-01', '2021-03-02');
                                $objectGeneral[$object->id]['2021_2'] = $object->getComingAmountByPeriodFromCustomers('2021-03-03', '2021-12-31');
                                $objectGeneral[$object->id]['2022'] = $object->getComingAmountByPeriodFromCustomers('2022-01-01', '2022-12-31');
                                $objectGeneral[$object->id]['total'] = $object->getComingAmountByPeriodFromCustomers('2017-01-01', '2022-12-31');

                                $objectsTotalCuming['2017'] += $objectGeneral[$object->id]['2017'];
                                $objectsTotalCuming['2018'] += $objectGeneral[$object->id]['2018'];
                                $objectsTotalCuming['2019'] += $objectGeneral[$object->id]['2019'];
                                $objectsTotalCuming['2020'] += $objectGeneral[$object->id]['2020'];
                                $objectsTotalCuming['2021_1'] += $objectGeneral[$object->id]['2021_1'];
                                $objectsTotalCuming['2021_2'] += $objectGeneral[$object->id]['2021_2'];
                                $objectsTotalCuming['2022'] += $objectGeneral[$object->id]['2022'];
                                $objectsTotalCuming['total'] += $objectGeneral[$object->id]['total'];
                            @endphp
                        @endforeach

                        @foreach($objectGeneral as $info)
                            <tr>
                                <td class="bl ps-2">{{ $info['name'] }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['2017'], 'RUB', 0, true) }}</td>
                                <td>
                                    @if ($info['2017'] > 0)
                                        {{ \App\Models\CurrencyExchangeRate::format($info['2017'] / $objectsTotalCuming['2017'] * 100, '', 2) }}
                                    @endif
                                </td>
                                <td class="text-danger br">{{ \App\Models\CurrencyExchangeRate::format($general2017 * ($info['2017'] / $objectsTotalCuming['2017']), 'RUB', 0, true) }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['2018'], 'RUB', 0, true) }}</td>
                                <td>
                                    @if ($info['2018'] > 0)
                                        {{ \App\Models\CurrencyExchangeRate::format($info['2018'] / $objectsTotalCuming['2018'] * 100, '', 2) }}
                                    @endif
                                </td>
                                <td class="text-danger br">{{ \App\Models\CurrencyExchangeRate::format($general2018 * ($info['2018'] / $objectsTotalCuming['2018']), 'RUB', 0, true) }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['2019'], 'RUB', 0, true) }}</td>
                                <td>
                                    @if ($info['2019'] > 0)
                                        {{ \App\Models\CurrencyExchangeRate::format($info['2019'] / $objectsTotalCuming['2019'] * 100, '', 2) }}
                                    @endif
                                </td>
                                <td class="text-danger br">{{ \App\Models\CurrencyExchangeRate::format($general2019 * ($info['2019'] / $objectsTotalCuming['2019']), 'RUB', 0, true) }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['2020'], 'RUB', 0, true) }}</td>
                                <td>
                                    @if ($info['2020'] > 0)
                                        {{ \App\Models\CurrencyExchangeRate::format($info['2020'] / $objectsTotalCuming['2020'] * 100, '', 2) }}
                                    @endif
                                </td>
                                <td class="text-danger br">{{ \App\Models\CurrencyExchangeRate::format($general2020 * ($info['2020'] / $objectsTotalCuming['2020']), 'RUB', 0, true) }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['2021_1'], 'RUB', 0, true) }}</td>
                                <td>
                                    @if ($info['2021_1'] > 0)
                                        {{ \App\Models\CurrencyExchangeRate::format($info['2021_1'] / $objectsTotalCuming['2021_1'] * 100, '', 2) }}
                                    @endif
                                </td>
                                <td class="text-danger br">{{ \App\Models\CurrencyExchangeRate::format($general2021_1 * ($info['2021_1'] / $objectsTotalCuming['2021_1']), 'RUB', 0, true) }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['2021_2'], 'RUB', 0, true) }}</td>
                                <td>
                                    @if ($info['2021_2'] > 0)
                                        {{ \App\Models\CurrencyExchangeRate::format($info['2021_2'] / $objectsTotalCuming['2021_2'] * 100, '', 2) }}
                                    @endif
                                </td>
                                <td class="text-danger br">{{ \App\Models\CurrencyExchangeRate::format($general2021_2 * ($info['2021_2'] / $objectsTotalCuming['2021_2']), 'RUB', 0, true) }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['2022'], 'RUB', 0, true) }}</td>
                                <td>
                                    @if ($info['2022'] > 0)
                                        {{ \App\Models\CurrencyExchangeRate::format($info['2022'] / $objectsTotalCuming['2022'] * 100, '', 2) }}
                                    @endif
                                </td>
                                <td class="text-danger br">{{ \App\Models\CurrencyExchangeRate::format($general2022 * ($info['2022'] / $objectsTotalCuming['2022']), 'RUB', 0, true) }}</td>

                                <td class="text-success bl">{{ \App\Models\CurrencyExchangeRate::format($info['total'], 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger br">
                                    {{ \App\Models\CurrencyExchangeRate::format(($general2017 * ($info['2017'] / $objectsTotalCuming['2017'])) + ($general2018 * ($info['2018'] / $objectsTotalCuming['2018'])) + ($general2019 * ($info['2019'] / $objectsTotalCuming['2019'])) + ($general2020 * ($info['2020'] / $objectsTotalCuming['2020'])) + ($general2021_1 * ($info['2021_1'] / $objectsTotalCuming['2021_1'])) + ($general2021_2 * ($info['2021_2'] / $objectsTotalCuming['2021_2'])) + ($general2022 * ($info['2022'] / $objectsTotalCuming['2022'])), 'RUB', 0, true) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bl {
            border-left: 1px solid #eee !important;
        }

        .bt {
            border-top: 1px solid #eee !important;
        }

        .br {
            border-right: 1px solid #eee !important;
        }
    </style>
@endpush
