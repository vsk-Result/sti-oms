@extends('objects.layouts.show')

@section('object-tab-title', 'Договора')

@section('object-tab-content')
    @include('contracts.modals.filter')
    @include('acts.modals.line_chart_payments')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            @include('contracts.parts._main_contracts')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

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
        });

        $('.show-subcontracts').on('click', function(e) {
            e.preventDefault();

            if ($(this).hasClass('show-active')) {
                $('a').removeClass('show-active');
                $('tr').removeClass('contract-row-active');
                $('.subcontract-row').remove();
                return;
            }

            $('a').removeClass('show-active');
            $('tr').removeClass('contract-row-active');
            $('.subcontract-row').remove();

            const $tr = $(this).closest('tr');
            const url = $(this).data('show-subcontracts-url');
            const currency = $(this).data('currency');

            $(this).addClass('show-active');

            mainApp.sendAJAX(
                url,
                'GET',
                {
                    currency
                },
                (data) => {
                    $tr.after(data.contracts_view);
                    $tr.addClass('contract-row-active');
                },
                {},
                () => {
                    KTMenu.createInstances();
                },
            )
        });
    </script>
@endpush

@push('styles')
    <style>
        .subcontract-row {
            background-color: #fff1e1;
            border-top: 1px solid #ddc4c4 !important;
        }

        .contract-row-active, .contract-row-active:hover {
            background-color: bisque !important;
            --bs-table-accent-bg: bisque !important;
        }

        .show-subcontracts.show-active {
            color: #ff8100 !important
        }
    </style>
@endpush
