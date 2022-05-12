@extends('layouts.app')

@section('toolbar-title', 'Акты')
@section('breadcrumbs', Breadcrumbs::render('acts.index'))

@section('content')
    @include('acts.modals.filter')
    @include('acts.modals.line_chart_payments')
    <div class="post">
        @include('acts.partials._acts')
    </div>
@endsection

@push('scripts')
    <script>
        var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
        var baseColor = KTUtil.getCssVariableValue('--bs-info');
        var lightColor = KTUtil.getCssVariableValue('--bs-light-info');
        var options = {
            series: [{
                name: "Оплачено",
                data: $('#lineChartPaymentActModal').data('chart-data')
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'line',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {},
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [baseColor]
            },
            xaxis: {
                categories: $('#lineChartPaymentActModal').data('chart-dates'),
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                labels: {
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
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    },
                    formatter: function (val) {
                        return new Intl.NumberFormat('ru-RU', {maximumFractionDigits: 0}).format(val)
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
                        return new Intl.NumberFormat('ru-RU', {maximumFractionDigits: 0}).format(val)
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

        var chart = new ApexCharts(document.querySelector("#lineChartPaymentAct"), options);
        chart.render();
    </script>
@endpush

