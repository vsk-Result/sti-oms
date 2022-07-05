@extends('layouts.app')

@section('toolbar-title', 'Распределение общих затрат')
@section('breadcrumbs', Breadcrumbs::render('general_costs.index'))

@section('content')
    @php
        $object27_1 = \App\Models\Object\BObject::where('code', '27.1')->first();
        $object27_8 = \App\Models\Object\BObject::where('code', '27.8')->first();
        $general2017 = \App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount_without_nds') + \App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_1->id)->sum('amount_without_nds') + (\App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_8->id)->sum('amount_without_nds') * 0.7);
        $general2018 = 21421114 + \App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount_without_nds') + \App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_1->id)->sum('amount_without_nds') + (\App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_8->id)->sum('amount_without_nds') * 0.7);
        $general2019 = 39760000 + 692048 + \App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount_without_nds') + \App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_1->id)->sum('amount_without_nds') + (\App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_8->id)->sum('amount_without_nds') * 0.7);
        $general2020 = 2000000 + 418000 + 1615000 + \App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount_without_nds') + \App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_1->id)->sum('amount_without_nds') + (\App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_8->id)->sum('amount_without_nds') * 0.7);
        $general2021_1 = 600000 + \App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount_without_nds') + \App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_1->id)->sum('amount_without_nds') + (\App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_8->id)->sum('amount_without_nds') * 0.7);
        $general2021_2 = 600000 + 68689966 + \App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount_without_nds') + \App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_1->id)->sum('amount_without_nds') + (\App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_8->id)->sum('amount_without_nds') * 0.7);
        $general2022 = \App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount_without_nds') + \App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-12-31'])->where('object_id', $object27_1->id)->sum('amount_without_nds') + (\App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-12-31'])->where('object_id', $object27_8->id)->sum('amount_without_nds') * 0.7);
        $generalTotal = $general2017 + $general2018 + $general2019 + $general2020 + $general2021_1 + $general2021_2 + $general2022;
        $info2017 = \App\Services\ObjectService::getGeneralCostsByPeriod('2017-01-01', '2017-12-31');
        $info2018 = \App\Services\ObjectService::getGeneralCostsByPeriod('2018-01-01', '2018-12-31', 21421114);
        $info2019 = \App\Services\ObjectService::getGeneralCostsByPeriod('2019-01-01', '2019-12-31', (39760000 + 692048));
        $info2020 = \App\Services\ObjectService::getGeneralCostsByPeriod('2020-01-01', '2020-12-31', (2000000 + 418000 + 1615000));
        $info2021_1 = \App\Services\ObjectService::getGeneralCostsByPeriod('2021-01-01', '2021-03-02', 600000);
        $info2021_2 = \App\Services\ObjectService::getGeneralCostsByPeriod('2021-03-03', '2021-12-31', (600000 + 68689966));
        $info2022 = \App\Services\ObjectService::getGeneralCostsByPeriod('2022-01-01', '2022-12-31');
    @endphp
    <div class="card mb-5 mb-xl-8 p-0 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-250px bt bl ps-2">Разбивка общих затрат по годам</th>

                            <th class="min-w-125px bt bl">Итого</th>
                            <th class="min-w-125px text-danger bt br hl text-right">{{ \App\Models\CurrencyExchangeRate::format($generalTotal, 'RUB') }}</th>

                            <th class="min-w-125px bt">2022</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2022, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">с 01.01.2021 по 02.03.21</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2021_1, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">с 03.03.2021 по 31.12.21</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2021_2, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2020</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2020, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2019</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2019, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2018</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2018, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">2017</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2017, 'RUB') }}</th>
                        </tr>
                        <tr>
                            <th class="min-w-250px bl ps-2">Объект</th>

                            <th class="min-w-125px bl text-center">Получено</th>
                            <th class="min-w-125px br hl text-center">Общие расходы</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

                            <th class="min-w-125px bl">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($objects as $object)
                            @if ($object->code == 288)
                                <tr>
                                    <td class="bl ps-2">{{ $object->getName() . ' | 1 (Строительство)' }}</td>

                                    @php
                                        $totalCuming = ($info2017[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2018[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2019[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2020[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2021_1[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2021_2[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2022[$object->id.'|1']['cuming_amount'] ?? 0);
                                        $totalGeneral = ($info2017[$object->id.'|1']['general_amount'] ?? 0) + ($info2018[$object->id.'|1']['general_amount'] ?? 0) + ($info2019[$object->id.'|1']['general_amount'] ?? 0) + ($info2020[$object->id.'|1']['general_amount'] ?? 0) + ($info2021_1[$object->id.'|1']['general_amount'] ?? 0) + ($info2021_2[$object->id.'|1']['general_amount'] ?? 0) + ($info2022[$object->id.'|1']['general_amount'] ?? 0);
                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                    @endphp

                                    <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @if (isset ($info2022[$object->id.'|1']))
                                        <td class="text-success text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td>-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_1[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_2[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2020[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2020[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2020[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2019[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2019[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2019[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2018[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2018[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2018[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2017[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2017[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2017[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td class="bl ps-2">{{ $object->getName() . ' | 2+4 (Инженерия)' }}</td>

                                    @php
                                        $totalCuming = ($info2017[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2018[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2019[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2020[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2021_1[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2021_2[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2022[$object->id.'|24']['cuming_amount'] ?? 0);
                                        $totalGeneral = ($info2017[$object->id.'|24']['general_amount'] ?? 0) + ($info2018[$object->id.'|24']['general_amount'] ?? 0) + ($info2019[$object->id.'|24']['general_amount'] ?? 0) + ($info2020[$object->id.'|24']['general_amount'] ?? 0) + ($info2021_1[$object->id.'|24']['general_amount'] ?? 0) + ($info2021_2[$object->id.'|24']['general_amount'] ?? 0) + ($info2022[$object->id.'|24']['general_amount'] ?? 0);
                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral, true, false);
                                    @endphp

                                    <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @if (isset ($info2022[$object->id.'|24']))
                                        <td class="text-success text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td>-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_1[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_2[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2020[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2020[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2020[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2019[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2019[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2019[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2018[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2018[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2018[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2017[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2017[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2017[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif
                                </tr>
                            @else
                                <tr>
                                    <td class="bl ps-2">{{ $object->getName() }}</td>

                                    @php
                                        $totalCuming = ($info2017[$object->id]['cuming_amount'] ?? 0) + ($info2018[$object->id]['cuming_amount'] ?? 0) + ($info2019[$object->id]['cuming_amount'] ?? 0) + ($info2020[$object->id]['cuming_amount'] ?? 0) + ($info2021_1[$object->id]['cuming_amount'] ?? 0) + ($info2021_2[$object->id]['cuming_amount'] ?? 0) + ($info2022[$object->id]['cuming_amount'] ?? 0);
                                        $totalGeneral = ($info2017[$object->id]['general_amount'] ?? 0) + ($info2018[$object->id]['general_amount'] ?? 0) + ($info2019[$object->id]['general_amount'] ?? 0) + ($info2020[$object->id]['general_amount'] ?? 0) + ($info2021_1[$object->id]['general_amount'] ?? 0) + ($info2021_2[$object->id]['general_amount'] ?? 0) + ($info2022[$object->id]['general_amount'] ?? 0);
                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                    @endphp

                                    <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @if (isset ($info2022[$object->id]))
                                        <td class="text-success text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td>-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_1[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_2[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2020[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2020[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2020[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2019[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2019[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2019[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2018[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2018[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2018[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2017[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2017[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2017[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif
                                </tr>
                            @endif
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

        .hl, .table tbody tr:last-child td.hl {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
            border: 1px dashed #ccc !important;
        }

        .text-right {
            text-align: right !important;
        }
    </style>
@endpush
