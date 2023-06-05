@extends('layouts.app')

@section('toolbar-title', 'Распределение общих затрат')
@section('breadcrumbs', Breadcrumbs::render('general_costs.index'))

@section('content')
    @php
        $object27_1 = \App\Models\Object\BObject::where('code', '27.1')->first();
        $object27_8 = \App\Models\Object\BObject::where('code', '27.8')->first();
        $general2017 = \App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2017-01-01', '2017-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2018 = 21421114 + \App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2018-01-01', '2018-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2019 = 39760000 + 692048 + \App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2019-01-01', '2019-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2020 = 2000000 + 418000 + 1615000 + \App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2020-01-01', '2020-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $general2021_1 = 600000 + \App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2021-01-01', '2021-03-02'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2021_2 = 600000 + 68689966 + \App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2021-03-03', '2021-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $general2022_1 = \App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2022-01-01', '2022-10-11'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);
        $general2022_2 = \App\Models\Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2022-10-12', '2022-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $general2023 = \App\Models\Payment::whereBetween('date', ['2023-01-01', '2023-12-31'])->where('type_id', \App\Models\Payment::TYPE_GENERAL)->sum('amount') + \App\Models\Payment::whereBetween('date', ['2023-01-01', '2023-12-31'])->where('object_id', $object27_1->id)->sum('amount') + (\App\Models\Payment::whereBetween('date', ['2023-01-01', '2023-12-31'])->where('object_id', $object27_8->id)->sum('amount') * 0.7);

        $generalTotal = $general2017 + $general2018 + $general2019 + $general2020 + $general2021_1 + $general2021_2 + $general2022_1 + $general2022_2 + $general2023;
        $info2017 = \App\Services\ObjectService::getGeneralCostsByPeriod('2017-01-01', '2017-12-31');
        $info2018 = \App\Services\ObjectService::getGeneralCostsByPeriod('2018-01-01', '2018-12-31', 21421114);
        $info2019 = \App\Services\ObjectService::getGeneralCostsByPeriod('2019-01-01', '2019-12-31', (39760000 + 692048));
        $info2020 = \App\Services\ObjectService::getGeneralCostsByPeriod('2020-01-01', '2020-12-31', (2000000 + 418000 + 1615000));
        $info2021_1 = \App\Services\ObjectService::getGeneralCostsByPeriod('2021-01-01', '2021-03-02', 600000);
        $info2021_2 = \App\Services\ObjectService::getGeneralCostsByPeriod('2021-03-03', '2021-12-31', (600000 + 68689966));
        $info2022_1 = \App\Services\ObjectService::getGeneralCostsByPeriod('2022-01-01', '2022-10-11');
        $info2022_2 = \App\Services\ObjectService::getGeneralCostsByPeriod('2022-10-12', '2022-12-31');
        $info2023 = \App\Services\ObjectService::getGeneralCostsByPeriod('2023-01-01', '2023-12-31');
    @endphp
    <div class="card mb-5 mb-xl-8 p-0 border-0">
        <div class="card-header border-0 pt-6 pe-0">
            <div class="card-title"></div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('general_costs.exports.store') }}" method="POST" class="hidden">
                        @csrf
                        <a
                                href="javascript:void(0);"
                                class="btn btn-light-primary"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                </svg>
                            </span>
                            Экспорт в Excel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive freeze-table">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead class="text-gray-600">
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-250px bt bl ps-2">Разбивка общих затрат по годам</th>

                            <th class="min-w-125px bt bl hl">Итого</th>
                            <th class="bt bl hl" style="min-width: 100px !important;width: 100px !important;"></th>
                            <th class="min-w-125px text-danger bt br hl text-right">{{ \App\Models\CurrencyExchangeRate::format($generalTotal, 'RUB') }}</th>

                            <th class="min-w-125px bt">2023</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2023, 'RUB') }}</th>

                            <th class="min-w-125px bt">с 31.12.22 по 12.10.22</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2022_2, 'RUB') }}</th>

                            <th class="min-w-125px bt">с 11.10.22 по 01.01.2022</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2022_1, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">с 31.12.21 по 03.03.21</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2021_2, 'RUB') }}</th>

                            <th class="min-w-125px bt bl">с 02.03.21 по 01.01.2021</th>
                            <th class="min-w-125px text-danger bt br text-right">{{ \App\Models\CurrencyExchangeRate::format($general2021_1, 'RUB') }}</th>

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

                            <th class="min-w-125px bl hl text-center">Получено</th>
                            <th class="bl hl text-center" style="min-width: 100px !important;width: 100px !important;">%</th>
                            <th class="min-w-125px br hl text-center">Общие расходы</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-125px br">Общие расходы на объект</th>

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
                                        $totalCuming = ($info2017[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2018[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2019[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2020[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2021_1[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2021_2[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2022_1[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2022_2[$object->id.'|1']['cuming_amount'] ?? 0) + ($info2023[$object->id.'|1']['cuming_amount'] ?? 0);
                                        $totalGeneral = ($info2017[$object->id.'|1']['general_amount'] ?? 0) + ($info2018[$object->id.'|1']['general_amount'] ?? 0) + ($info2019[$object->id.'|1']['general_amount'] ?? 0) + ($info2020[$object->id.'|1']['general_amount'] ?? 0) + ($info2021_1[$object->id.'|1']['general_amount'] ?? 0) + ($info2021_2[$object->id.'|1']['general_amount'] ?? 0) + ($info2022_1[$object->id.'|1']['general_amount'] ?? 0) + ($info2022_2[$object->id.'|1']['general_amount'] ?? 0) + ($info2023[$object->id.'|1']['general_amount'] ?? 0);
                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                    @endphp

                                    <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="bl hl text-center" style="min-width: 100px !important;width: 100px !important;">{{ number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2) }}%</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @if (isset ($info2023[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2023[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2023[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2022_2[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_2[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_2[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2022_1[$object->id.'|1']))
                                        <td class="text-success text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_1[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_1[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td>-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_2[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_1[$object->id.'|1']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|1']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|1']['general_amount'], 'RUB', 0, true) }}</td>
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
                                        $totalCuming = ($info2017[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2018[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2019[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2020[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2021_1[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2021_2[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2022[$object->id.'|24']['cuming_amount'] ?? 0) + ($info2023[$object->id.'|24']['cuming_amount'] ?? 0);
                                        $totalGeneral = ($info2017[$object->id.'|24']['general_amount'] ?? 0) + ($info2018[$object->id.'|24']['general_amount'] ?? 0) + ($info2019[$object->id.'|24']['general_amount'] ?? 0) + ($info2020[$object->id.'|24']['general_amount'] ?? 0) + ($info2021_1[$object->id.'|24']['general_amount'] ?? 0) + ($info2021_2[$object->id.'|24']['general_amount'] ?? 0) + ($info2022[$object->id.'|24']['general_amount'] ?? 0) + ($info2023[$object->id.'|24']['general_amount'] ?? 0);
                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral, true, false);
                                    @endphp

                                    <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="bl hl text-center" style="min-width: 100px !important;width: 100px !important;">{{ number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2) }}%</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @if (isset ($info2023[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2023[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2023[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2022_2[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_2[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_2[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2022_1[$object->id.'|24']))
                                        <td class="text-success text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_1[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_1[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td>-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_2[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_1[$object->id.'|24']))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|24']['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id.'|24']['general_amount'], 'RUB', 0, true) }}</td>
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
                                        $totalCuming = ($info2017[$object->id]['cuming_amount'] ?? 0) + ($info2018[$object->id]['cuming_amount'] ?? 0) + ($info2019[$object->id]['cuming_amount'] ?? 0) + ($info2020[$object->id]['cuming_amount'] ?? 0) + ($info2021_1[$object->id]['cuming_amount'] ?? 0) + ($info2021_2[$object->id]['cuming_amount'] ?? 0) + ($info2022_1[$object->id]['cuming_amount'] ?? 0) + ($info2022_2[$object->id]['cuming_amount'] ?? 0) + ($info2023[$object->id]['cuming_amount'] ?? 0);
                                        $totalGeneral = ($info2017[$object->id]['general_amount'] ?? 0) + ($info2018[$object->id]['general_amount'] ?? 0) + ($info2019[$object->id]['general_amount'] ?? 0) + ($info2020[$object->id]['general_amount'] ?? 0) + ($info2021_1[$object->id]['general_amount'] ?? 0) + ($info2021_2[$object->id]['general_amount'] ?? 0) + ($info2022_1[$object->id]['general_amount'] ?? 0) + ($info2022_2[$object->id]['general_amount'] ?? 0) + ($info2023[$object->id]['general_amount'] ?? 0);
                                        \App\Services\ObjectGeneralCostService::updateGeneralCost($object, $totalGeneral);
                                    @endphp

                                    <td class="text-success bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalCuming, 'RUB', 0, true) }}</td>
                                    <td class="bl hl text-center" style="min-width: 100px !important;width: 100px !important;">{{ number_format(($totalCuming > 0 ? abs($totalGeneral / $totalCuming) : 0) * 100, 2) }}%</td>
                                    <td class="text-danger bl hl text-right">{{ \App\Models\CurrencyExchangeRate::format($totalGeneral, 'RUB', 0, true) }}</td>

                                    @if (isset ($info2023[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2023[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2023[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2022_2[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_2[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_2[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2022_1[$object->id]))
                                        <td class="text-success text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_1[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2022_1[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td>-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_2[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_2[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
                                    @else
                                        <td class="bl">-</td>
                                        <td class="br">-</td>
                                    @endif

                                    @if (isset ($info2021_1[$object->id]))
                                        <td class="text-success bl text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id]['cuming_amount'], 'RUB', 0, true) }}</td>
                                        <td class="text-danger br text-right">{{ \App\Models\CurrencyExchangeRate::format($info2021_1[$object->id]['general_amount'], 'RUB', 0, true) }}</td>
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

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });
    </script>
@endpush

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
            min-width: 150px !important;
        }

        .text-right {
            text-align: right !important;
        }
    </style>
@endpush
