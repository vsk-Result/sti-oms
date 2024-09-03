@extends('layouts.app')

@section('title', 'Отчет по балансам')
@section('toolbar-title', 'Отчет по балансам')
@section('breadcrumbs', Breadcrumbs::render('pivots.balances.index'))

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <form class="form" action="{{ route('pivots.balances.index') }}" method="GET">
                <div class="row mb-5">
                    <div class="col-md-3 fv-row">
                        <div>
                            <label class="form-label fw-bolder text-dark fs-6">Период отчета</label>
                            <input
                                class="form-control form-control-lg form-control-solid date-range-picker"
                                type="text"
                                name="balance_date"
                                value="{{ \Carbon\Carbon::parse($period[0])->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($period[1])->format('d.m.Y') }}"
                                required
                            />
                        </div>
                        <button class="btn btn-primary mt-3">Применить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <span class="fs-2">Сводная таблица по счетам за период <strong>{{ \Carbon\Carbon::parse($period[0])->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($period[1])->format('d.m.Y') }}</strong></span>

    <div class="row mb-4 mt-4">
        <div class="col-md-12">
            <div class="card-body p-0 ps-0">
                <div class="table-responsive freeze-table">
                    <table class="table table-hover align-middle table-row-dashed fs-7">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th valign="middle" rowspan="2" class="min-w-100px ps-4 br">Свод</th>
                                <th valign="middle" class="total-column hl text-center">Итого</th>

                                @foreach($pivot['banks'] as $bankInfo)
                                    <th valign="middle" class="p-0 min-w-150px bb text-right {{ $loop->first ? 'bl' : '' }}">
                                        <div class="fs-8 d-flex flex-column justify-content-center align-items-center">
                                            <img width="{{ $bankInfo['logo'] === '/images/banks/vtb.png' ? '45%' : '70%' }}" src="{{ $bankInfo['logo'] }}" />
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                            <tr>
                                <th class="hl text-center">{{ \Carbon\Carbon::parse($period[0])->format('d.m.Y') }}</th>
                                @foreach($pivot['banks'] as $bankInfo)
                                    <th valign="middle" class="p-0 min-w-150px text-center fs-8 {{ $loop->first ? 'bl' : '' }}">
                                        {{ $bankInfo['name'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @foreach($pivot['entries']['start'] as $svod => $entry)
                                <tr>
                                    <td class="ps-4 br {{ $loop->first ? 'bt' : '' }}">
                                        {{ $svod }}
                                    </td>

                                    <td class="hl text-right {{ $loop->first ? 'bt' : '' }}">
                                        @foreach($entry['total'] as $currency => $amount)
                                            <span class="{{ $amount > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($amount, $currency, 0, true) }}</span>
                                            <br>
                                        @endforeach
                                    </td>

                                    @foreach($entry['banks'] as $currencies)
                                        <td class="bl text-right {{ $loop->first ? 'bt' : '' }} {{ $loop->last ? 'pe-4' : '' }}">
                                            @foreach($currencies as $currency => $amount)
                                                <span class="{{ $amount > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($amount, $currency, 0, true) }}</span>
                                                <br>
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach

                            <tr>
                                <th class="br"></th>
                                <th class="hl fw-bold text-center text-black">{{ \Carbon\Carbon::parse($period[1])->format('d.m.Y') }}</th>
                                <th class="bl"></th>
                            </tr>

                            @foreach($pivot['entries']['end'] as $svod => $entry)
                                <tr>
                                    <td class="ps-4 br {{ $loop->first ? 'bt' : '' }}">
                                        {{ $svod }}
                                    </td>

                                    <td class="hl text-right {{ $loop->first ? 'bt' : '' }}">
                                        @foreach($entry['total'] as $currency => $amount)
                                            <span class="{{ $amount > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($amount, $currency, 0, true) }}</span>
                                            <br>
                                        @endforeach
                                    </td>

                                    @foreach($entry['banks'] as $currencies)
                                        <td class="bl text-right {{ $loop->first ? 'bt' : '' }} {{ $loop->last ? 'pe-4' : '' }}">
                                            @foreach($currencies as $currency => $amount)
                                                <span class="{{ $amount > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($amount, $currency, 0, true) }}</span>
                                                <br>
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <span class="fs-2">Графики приходов и расходов за период <strong>{{ \Carbon\Carbon::parse($period[0])->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($period[1])->format('d.m.Y') }}</strong></span>

    <div class="row mt-4">
        <div class="col-lg-12 col-xl-12 col-xxl-6 mb-5 mb-xl-0">
            <div class="card card-flush overflow-hidden h-md-100">
                <div class="card-header py-2">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">График приходов</span>
                    </h3>
                </div>
                <div class="card-body d-flex justify-content-between flex-column pb-1 px-0 pt-0">
                    <div class="px-9 mb-5">
                        <div class="d-flex">
                            <span class="fs-2hx fw-bold me-2 lh-1 ls-n2 text-success">{{ \App\Models\CurrencyExchangeRate::format($pivot['entries']['total']['amount_receive']['RUB'], 'RUB', 0, true) }}</span>
                        </div>
                        <span class="fs-6 fw-semibold text-gray-400">Всего пришло средств за период</span>
                    </div>
                    <div
                        id="amount_receive_chart"
                        data-values="{!! json_encode($pivot['charts']['receive']['values']) !!}"
                        data-months="{{ json_encode($pivot['charts']['receive']['months']) }}"
                        data-max-values="{!! json_encode($pivot['charts']['receive']['max_values']) !!}"
                        data-min-values="{!! json_encode($pivot['charts']['receive']['min_values']) !!}"
                        class="min-h-auto ps-2"
                        style="height: 300px; min-height: 315px;"
                    ></div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-xl-12 col-xxl-6 mb-5 mb-xl-0">
            <div class="card card-flush overflow-hidden h-md-100">
                <div class="card-header py-2">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">График расходов</span>
                    </h3>
                </div>
                <div class="card-body d-flex justify-content-between flex-column pb-1 px-0 pt-0">
                    <div class="px-9 mb-5">
                        <div class="d-flex">
                            <span class="fs-2hx fw-bold me-2 lh-1 ls-n2 text-danger">{{ \App\Models\CurrencyExchangeRate::format($pivot['entries']['total']['amount_pay']['RUB'], 'RUB', 0, true) }}</span>
                        </div>
                        <span class="fs-6 fw-semibold text-gray-400">Всего потрачено средств за период</span>
                    </div>
                    <div
                        id="amount_pay_chart"
                        data-values="{!! json_encode($pivot['charts']['pay']['values']) !!}"
                        data-months="{{ json_encode($pivot['charts']['pay']['months']) }}"
                        data-max-values="{!! json_encode($pivot['charts']['pay']['max_values']) !!}"
                        data-min-values="{!! json_encode($pivot['charts']['pay']['min_values']) !!}"
                        class="min-h-auto ps-2"
                        style="height: 300px; min-height: 315px;"
                    ></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const formatter = new Intl.NumberFormat('ru-RU');

        $(function() {
            mainApp.initFreezeTable(2);

            const rchart = {
                self: null,
                rendered: false
            };
            const pchart = {
                self: null,
                rendered: false
            };

            const receiveChart = document.getElementById("amount_receive_chart");
            const payChart = document.getElementById("amount_pay_chart");

            const initReceiveChart = function(element, chart) {
                if (!element) {
                    return;
                }

                const jqElement = $(element);
                var height = parseInt(KTUtil.css(element, 'height'));
                var labelColor = '#565674';
                var borderColor = '#E1E3EA';
                var baseColor = '#50cd89';
                var lightColor = '#50cd89';

                var options = {
                    series: [{
                        name: 'Приходы:',
                        data: jqElement.data('values')
                    }],
                    chart: {
                        fontFamily: 'inherit',
                        type: 'area',
                        height: height,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {

                    },
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0,
                            stops: [0, 0, 0]
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        show: true,
                        width: 3,
                        colors: [baseColor]
                    },
                    xaxis: {
                        categories: jqElement.data('months'),
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false
                        },
                        tickAmount: jqElement.data('months').length,
                        labels: {
                            show: true,
                            rotate: -45,
                            rotateAlways: false,
                            hideOverlappingLabels: true,
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            }
                        },
                        crosshairs: {
                            position: 'front',
                            stroke: {
                                color: baseColor,
                                width: 1,
                                dashArray: 3
                            }
                        },
                        tooltip: {
                            enabled: true,
                            formatter: undefined,
                            offsetY: 0,
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        max: jqElement.data('maxValues'),
                        min: jqElement.data('minValues'),
                        tickAmount: 10,
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            },
                            formatter: function (val) {
                                return formatter.format(val.toFixed(0)) + " ₽"
                            }
                        }
                    },
                    states: {
                        normal: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        hover: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        active: {
                            allowMultipleDataPointsSelection: false,
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        }
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        },
                        y: {
                            formatter: function (val) {
                                return formatter.format(val.toFixed(0)) + " ₽"
                            }
                        }
                    },
                    colors: [lightColor],
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 4,
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    markers: {
                        strokeColor: baseColor,
                        strokeWidth: 3
                    }
                };

                chart.self = new ApexCharts(element, options);

                setTimeout(function() {
                    chart.self.render();
                    chart.rendered = true;
                }, 200);
            }
            const initPayChart = function(element, chart) {
                if (!element) {
                    return;
                }

                const jqElement = $(element);
                var height = parseInt(KTUtil.css(element, 'height'));
                var labelColor = '#565674';
                var borderColor = '#E1E3EA';
                var baseColor = '#f1416c';
                var lightColor = '#f1416c';

                var options = {
                    series: [{
                        name: 'Расходы:',
                        data: jqElement.data('values')
                    }],
                    chart: {
                        fontFamily: 'inherit',
                        type: 'area',
                        height: height,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {

                    },
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0,
                            stops: [0, 0, 0]
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        show: true,
                        width: 3,
                        colors: [baseColor]
                    },
                    xaxis: {
                        categories: jqElement.data('months'),
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false
                        },
                        tickAmount: jqElement.data('months').length,
                        labels: {
                            show: true,
                            rotate: -45,
                            rotateAlways: false,
                            hideOverlappingLabels: true,
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            }
                        },
                        crosshairs: {
                            position: 'front',
                            stroke: {
                                color: baseColor,
                                width: 1,
                                dashArray: 3
                            }
                        },
                        tooltip: {
                            enabled: true,
                            formatter: undefined,
                            offsetY: 0,
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        max: jqElement.data('maxValues'),
                        min: jqElement.data('minValues'),
                        tickAmount: 10,
                        labels: {
                            style: {
                                colors: labelColor,
                                fontSize: '12px'
                            },
                            formatter: function (val) {
                                return formatter.format(val.toFixed(0)) + " ₽"
                            }
                        }
                    },
                    states: {
                        normal: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        hover: {
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        },
                        active: {
                            allowMultipleDataPointsSelection: false,
                            filter: {
                                type: 'none',
                                value: 0
                            }
                        }
                    },
                    tooltip: {
                        style: {
                            fontSize: '12px'
                        },
                        y: {
                            formatter: function (val) {
                                return formatter.format(val.toFixed(0)) + " ₽"
                            }
                        }
                    },
                    colors: [lightColor],
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 4,
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    markers: {
                        strokeColor: baseColor,
                        strokeWidth: 3
                    }
                };

                chart.self = new ApexCharts(element, options);

                setTimeout(function() {
                    chart.self.render();
                    chart.rendered = true;
                }, 200);
            }

            initReceiveChart(receiveChart, rchart);
            initPayChart(payChart, pchart);
        });
    </script>
@endpush

@push('styles')
    <style>
        .table td, .table th {
            border: 1px solid #eee;
        }
        .bl {
            border-left: 1px dashed #ccc !important;
        }
        .br {
            border-right: 1px dashed #ccc !important;
        }
        .bb {
            border-bottom: 1px dashed #ccc !important;
        }
        .bt {
            border-top: 1px dashed #ccc !important;
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

        .total-column {
            min-width: 150px !important;
            width: 150px !important;
            max-width: 150px !important;
        }

        .table tbody tr:last-child td {
            border-bottom: 1px solid #eee !important;
        }
    </style>
@endpush
