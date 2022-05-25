@extends('layouts.app')

@section('toolbar-title', 'Распределение общих затрат')
@section('breadcrumbs', Breadcrumbs::render('general_costs.index'))

@section('content')
    <div class="card mb-5 mb-xl-8">
        <div class="card-header border-0 pt-6">
            <div class="card-title">

            </div>
            <div class="card-toolbar">

            </div>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-row-dashed fs-7">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-250px">Разбивка общих затрат по годам</th>

                            <th class="min-w-125px">2017</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>

                            <th class="min-w-125px">2018</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>

                            <th class="min-w-125px">2019</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>

                            <th class="min-w-125px">2020</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>

                            <th class="min-w-125px">с 01.01.2021 по 02.03.21</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>

                            <th class="min-w-125px">с 03.03.2021 по 31.12.21</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>

                            <th class="min-w-125px">2022</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>

                            <th class="min-w-125px">Итого</th>
                            <th class="min-w-75px"></th>
                            <th class="min-w-125px"></th>
                        </tr>
                        <tr>
                            <th class="min-w-250px">Объект</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px">Общие расходы на объект</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px">Общие расходы на объект</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px">Общие расходы на объект</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px">Общие расходы на объект</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px">Общие расходы на объект</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px">Общие расходы на объект</th>

                            <th class="min-w-125px">Получено</th>
                            <th class="min-w-75px">%</th>
                            <th class="min-w-125px">Общие расходы на объект</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($objects as $object)
                            <tr>
                                <td>{{ $object->getName() }}</td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2017-01-01', '2017-12-31'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2018-01-01', '2018-12-31'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2019-01-01', '2019-12-31'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2020-01-01', '2020-12-31'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2021-01-01', '2021-03-02'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2021-03-03', '2021-12-31'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2022-01-01', '2022-12-31'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>

                                <td class="text-success">{{ \App\Models\CurrencyExchangeRate::format($object->getComingAmountByPeriodFromCustomers('2017-01-01', '2022-12-31'), 'RUB', 0, true) }}</td>
                                <td></td>
                                <td class="text-danger"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
