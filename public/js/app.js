"use strict";

const mainApp = function() {
    const initAjaxSetup = function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    const initSetToastrOptions = function() {
        toastr.options = {
            "timeOut": "2000",
        };
    }

    const initShowDateAndTime = function() {
        const date = new Date();
        $('#show-date-and-time').text(date.toLocaleString());
    }

    const initDateRangePicker = function() {
        $(".date-range-picker").daterangepicker({
            autoUpdateInput: false,
            locale: {
                firstDay: 1,
                format: "DD.MM.Y",
                cancelLabel: 'Очистить',
                separator: " - ",
                applyLabel: "Применить",
                daysOfWeek: [
                    "Вс",
                    "Пн",
                    "Вт",
                    "Ср",
                    "Чт",
                    "Пт",
                    "Сб"
                ],
                monthNames: [
                    "Январь",
                    "Февраль",
                    "Март",
                    "Апрель",
                    "Май",
                    "Июнь",
                    "Июль",
                    "Август",
                    "Сентябрь",
                    "Октябрь",
                    "Ноябрь",
                    "Декабрь"
                ],
            }
        });

        $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.Y') + ' - ' + picker.endDate.format('DD.MM.Y'));
        });

        $('.date-range-picker').on('cancel.daterangepicker', function() {
            $(this).val('');
        });

        $('.date-range-picker-single').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            showDropdowns: true,
            locale: {
                firstDay: 1,
                format: "Y-MM-DD",
                cancelLabel: 'Очистить',
                separator: " - ",
                applyLabel: "Применить",
                daysOfWeek: [
                    "Вс",
                    "Пн",
                    "Вт",
                    "Ср",
                    "Чт",
                    "Пт",
                    "Сб"
                ],
                monthNames: [
                    "Январь",
                    "Февраль",
                    "Март",
                    "Апрель",
                    "Май",
                    "Июнь",
                    "Июль",
                    "Август",
                    "Сентябрь",
                    "Октябрь",
                    "Ноябрь",
                    "Декабрь"
                ],
            }
        });

        $('.date-range-picker-single').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('Y-MM-DD'));
        });

        $('.date-range-picker-single').on('cancel.daterangepicker', function() {
            $(this).val('');
        });
    }

    const initSendAJAX = function (url, type, data, successCallback, errorCallback, alwaysCallback) {
        url = url || '';
        type = type || 'POST';
        data = data || {};
        successCallback = successCallback || null;
        errorCallback = errorCallback || null;
        alwaysCallback = alwaysCallback || null;

        $.ajax({
            url: url,
            type: type,
            data: data,
        })
        .done(function(data) {
            if (data.status === 'success') {
                if (successCallback) {
                    successCallback(data);
                }
                if (data.message) {
                    toastr.success(data.message);
                }
            } else if (data.status === 'error') {
                if (errorCallback) {
                    errorCallback(data);
                }
                toastr.error('Ошибка. ' + data.message);
            }
        })
        .fail(function(xhr) {
            if (xhr.status === 419) {
                toastr.error('Ошибка сессии. Автоматическая перезагрузка страницы через 1 сек.');
                setTimeout(() => {
                    window.location.reload(false);
                }, 1000);
            } else {
                toastr.error('Неизвестная ошибка. Обновите страницу и попробуйте снова.');
            }
        })
        .always(function() {
            if (alwaysCallback) {
                alwaysCallback();
            }
        });
    }

    const initFavouriteLinksCreateHandler = function() {
        $('#create-favourite-link-submit').on('click', function() {
            const name = $('input[name=favourite_link_name]').val();
            if (name.length === 0) {
                return false;
            }
            const $modal = $('#createFavouriteLinkModal')
            const url = $modal.data('store-favourite-link-url');
            const link = location.pathname + location.search;
            initSendAJAX(
                url,
                'POST',
                {link, name},
                () => {
                    $modal.modal('hide');
                }
            );
        });
    }

    const initActsPaymentsLineChart = function() {
        const $modal = $('#lineChartPaymentActModal');

        if ($modal.length === 0) {
            return false;
        }

        const labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
        const borderColor = KTUtil.getCssVariableValue('--bs-gray-200');
        const baseColor = KTUtil.getCssVariableValue('--bs-info');
        const lightColor = KTUtil.getCssVariableValue('--bs-light-info');

        const optionsRUB = {
            series: [{
                name: "Оплачено",
                data: $modal.data('rub-chart-amounts')
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
                categories: $modal.data('rub-chart-months'),
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
        const optionsEUR = {
            series: [{
                name: "Оплачено",
                data: $modal.data('eur-chart-amounts')
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
                categories: $modal.data('eur-chart-months'),
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

        let chart = new ApexCharts(document.querySelector("#lineChartPaymentActRUB"), optionsRUB);
        chart.render();

        chart = new ApexCharts(document.querySelector("#lineChartPaymentActEUR"), optionsEUR);
        chart.render();
    }

    return {
        init: function () {
            initAjaxSetup();
            initSetToastrOptions();
            initFavouriteLinksCreateHandler();
            initDateRangePicker();
            initActsPaymentsLineChart();

            if ($('#show-date-and-time').length > 0) {
                setInterval(initShowDateAndTime, 500);
            }
        },
        sendAJAX: function (url, type, data, successCallback, errorCallback, alwaysCallback) {
            initSendAJAX(url, type, data, successCallback, errorCallback, alwaysCallback);
        },
    };
}();

KTUtil.onDOMContentLoaded(function () {
    mainApp.init();
});

$(document).on('click', '.btn-copy', function() {
    copyToClipboard(this);
    toastr.success('Баланс успешно скопирован.');
});

function copyToClipboard(element) {
    const $temp = $("<input>");
    $("body").append($temp);

    const value = $(element).data('clipboard-value').toString().replace('.', ',');

    $temp.val(value).select();

    document.execCommand("copy");

    $temp.remove();
}
