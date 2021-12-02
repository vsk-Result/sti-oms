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
                    </div>

                    @can('create bank-guarantee')
                        <a target="_blank" href="{{ route('bank_guarantees.create') }}" class="btn btn-sm btn-light-primary align-self-center">Добавить банковскую гарантию</a>
                    @endcan
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Компания</th>
                                <th>Банк</th>
                                <th>Дата начала БГ</th>
                                <th>Дата окончания БГ</th>
                                <th>Сумма БГ</th>
                                <th>Дата начала депозита</th>
                                <th>Дата окончания депозита</th>
                                <th>Сумма депозита</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($object->bankGuarantees as $guarantee)
                                <tr>
                                    <td>{!! $guarantee->company->getShortNameColored() !!}</td>
                                    <td>{{ $guarantee->getBankName() }}</td>
                                    <td>{{ $guarantee->getStartDateFormatted() }}</td>
                                    <td>{{ $guarantee->getEndDateFormatted() }}</td>
                                    <td>{{ $guarantee->getAmount() }}</td>
                                    <td>{{ $guarantee->getStartDateDepositFormatted() }}</td>
                                    <td>{{ $guarantee->getEndDateDepositFormatted() }}</td>
                                    <td>{{ $guarantee->getAmountDeposit() }}</td>
                                    <td>
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                            <span class="svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                            </svg>
                                        </span>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                            @can('show bank-guarantees')
                                                <div class="menu-item px-3">
                                                    <a target="_blank" href="{{ route('bank_guarantees.show', $guarantee) }}" class="menu-link px-3">Посмотреть</a>
                                                </div>
                                            @endcan
                                            @can('edit bank-guarantees')
                                                <div class="menu-item px-3">
                                                    <a target="_blank" href="{{ route('bank_guarantees.edit', $guarantee) }}" class="menu-link px-3">Изменить</a>
                                                </div>

                                                <div class="menu-item px-3">
                                                    <form action="{{ route('bank_guarantees.destroy', $guarantee) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                            href="#"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить банковскую гарантию?')) {this.closest('form').submit();}"
                                                        >
                                                            Удалить
                                                        </a>
                                                    </form>
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Банковские гарантии отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
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
