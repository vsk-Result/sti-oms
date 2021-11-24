@extends('objects.layouts.show')

@section('object-tab-title', 'Сводная информация')

@section('object-tab-content')
    <div class="row g-6 g-xl-9 mb-4">
        <div class="col-lg-4">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Долги</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table fs-6 fw-bold">
                        <tbody>
                            <tr>
                                <td>Долг подрядчикам</td>
                                <td class="text-danger">13 245 620</td>
                            </tr>
                            <tr>
                                <td>Долг за материалы</td>
                                <td class="text-danger">10 773 800</td>
                            </tr>
                            <tr>
                                <td>Долг подписанных актов</td>
                                <td class="text-success">39 467 255</td>
                            </tr>
                            <tr>
                                <td>Долг гарантийного удержания</td>
                                <td class="text-success">44 900 039</td>
                            </tr>
                            <tr>
                                <td>Долг на зарплаты</td>
                                <td class="text-danger">14 211 590</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Количество ИТР и рабочих</h3>
                        <div class="fs-6 fw-bold text-gray-400">За последние пол года</div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    <div id="chart-itr-workers-count"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Зарплата рабочим</h3>
                        <div class="fs-6 fw-bold text-gray-400">За последние пол года</div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    <div id="chart-workers-salary"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Банковские гарантии и депозиты</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>

                    <a href="#" class="btn btn-sm btn-light-primary align-self-center">Добавить банковскую гарантию</a>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Банк</th>
                                <th>Дата начала БГ</th>
                                <th>Дата окончания БГ</th>
                                <th>Сумма БГ</th>
                                <th>Дата начала депозита</th>
                                <th>Дата окончания депозита</th>
                                <th>Сумма депозита</th>
                                <th>Действие</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td>ПАО "ВТБ"</td>
                                <td>29.08.2021</td>
                                <td>28.09.2021</td>
                                <td>9 456 000</td>
                                <td>06.10.2021</td>
                                <td>16.11.2021</td>
                                <td>5 370 260</td>
                                <td>
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                            <tr>
                                <td>ПАО "Сбербанк"</td>
                                <td>15.10.2021</td>
                                <td>16.11.2021</td>
                                <td>5 760 000</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                            <tr>
                                <td>ПАО "ВТБ"</td>
                                <td>06.10.2021</td>
                                <td>16.11.2021</td>
                                <td>5 370 260</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var colorPalette = ['#f15a22','#269ffb'];
        var optionsBar = {
            chart: {
                type: 'bar',
                height: 250,
                width: '100%',
                stacked: true,
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                }
            },
            colors: colorPalette,
            series: [{
                name: "Рабочие",
                data: [42, 52, 16, 55, 59, 51],
            }, {
                name: "ИТР",
                data: [6, 12, 4, 7, 5, 3],
            }],
            labels: ['Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь'],
            dataLabels: {
                enabled: true
            },
            xaxis: {
                labels: {
                    show: true
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#78909c'
                    }
                }
            },
            title: {}
        }

        var chartBar = new ApexCharts(document.querySelector('#chart-itr-workers-count'), optionsBar);
        chartBar.render();

        options = {
            series: [{
                name: "Рабочие",
                data: [85471354, 86134892, 87137925, 88923559, 89261895, 90133544]
            }],
            chart: {
                height: 250,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            markers: {
                size: 6,
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: 'straight'
            },
            title: {},
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь'],
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return new Intl.NumberFormat('ru-RU').format(value);
                    }
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart-workers-salary"), options);
        chart.render();
    </script>
@endpush
