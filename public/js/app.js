"use strict";

const mainApp = function() {
    const initAjaxSetup = function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    const initInputAmountMask = function() {
        $('.amount-mask').each(function() {

            $(this).val($(this).val().replace('.', ','));

        });


        Inputmask("decimal", {
            digits: '17,2',
            radixPoint: ',',
            groupSeparator: ' ',
            inputtype: "text"
        }).mask(".amount-mask");

        $('.amount-mask').each(function() {
            const value = $(this).val();
            $(this).attr('autocomplete', 'off');
            $(this).before(`<div><button title="Скопировать" type="button" class="btn btn-icon btn-sm btn-light btn-copy">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
            </button></div>`);
        });
    }

    // Исключительно в научных целях (кто прочитал, тот пати пупер)
    const initDmitryPartyPooper = function() {
        const isDmitryRightNow = $('body').data('is-dmitry') === 1;
        const images = ['happy.jpg', 'sad.jpg'];
        const imagePath = '/images/you/must/not/visit/this/folder/';
        const availableKeyCodes = [1076, 1080, 1084, 1072, 1044, 1048, 1052, 1040]; // димаДИМА
        const bodyWidth = document.body.clientWidth
        const bodyHeight = document.body.clientHeight;

        if (isDmitryRightNow) {
            $('<img>', {
                class: 'dmitry-love',
                style: 'position: absolute; top: 0; left: 0; width: 300px; height: auto;',
                src: ''
            }).appendTo('body');
        }

        $(document).on("keypress", function (e) {
            if (! isDmitryRightNow) {
                return;
            }

            if (availableKeyCodes.indexOf(e.which) !== -1) {
                const randPosX = Math.floor((Math.random() * bodyWidth));
                const randPosY = Math.floor((Math.random() * bodyHeight));
                const randomImageIndex = Math.round(Math.random());

                $('<img>', {
                    class: 'dmitry-love',
                    style: `cursor: pointer;position: absolute; top: ${randPosY}px; left: ${randPosX}px; width: 200px; height: auto;`,
                    src: imagePath + images[randomImageIndex]
                }).appendTo('body');
            }
        });

        $(document).on('click', '.dmitry-love', function () {
            $(this).remove();
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

        return $.ajax({
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
            if (statusText === 'abort') {
                return;
            }

            if (xhr.status === 419) {
                toastr.error('Ошибка сессии. Автоматическая перезагрузка страницы через 1 сек.');
                setTimeout(() => {
                    window.location.reload(false);
                }, 1000);
            } else {
                console.log('xhr', xhr);
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
                name: "Выполнено",
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
                name: "Выполнено",
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

    const initFreezeTable = function(columnNum, freezeHead, freezeColumn) {
        columnNum = columnNum || 1;
        freezeHead = freezeHead || true;
        freezeColumn = freezeColumn || true;

        $(".freeze-table").freezeTable({
            freezeHead: freezeHead,
            freezeColumn: freezeColumn,
            columnNum: columnNum,
            fixedNavbar: '#kt_header',
        });
    }

    return {
        init: function () {
            initAjaxSetup();
            initSetToastrOptions();
            initFavouriteLinksCreateHandler();
            initDateRangePicker();
            initActsPaymentsLineChart();

            initDmitryPartyPooper();
            initInputAmountMask();

            if ($('#show-date-and-time').length > 0) {
                setInterval(initShowDateAndTime, 500);
            }
        },
        sendAJAX: function (url, type, data, successCallback, errorCallback, alwaysCallback) {
            return initSendAJAX(url, type, data, successCallback, errorCallback, alwaysCallback);
        },
        initFreezeTable: function(columnNum, freezeHead, freezeColumn) {
            initFreezeTable(columnNum, freezeHead, freezeColumn);
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    mainApp.init();
});

$(document).on('click', '.btn-copy', function() {
    copyToClipboard(this);
    toastr.success('Значение успешно скопировано.');
});

function copyToClipboard(element) {
    const $temp = $("<input>");
    $("body").append($temp);

    let value;

    if ($(element).data('clipboard-value')) {
        value = $(element).data('clipboard-value');
    } else {
        value = $(element).parent().parent().find('input').first().val();
    }

    value = value.toString().replace('.', ',').replace(' ', '');

    $temp.val(value).select();

    document.execCommand("copy");

    $temp.remove();
}

$(document).on('click', '.check-nds', function(e) {
    const checked = this.checked;
    const target = $(this).data('target');

    $(`.${target}`).each(function() {
        if (checked) {
            $(this).text($(this).data('amount-nds'));
        } else {
            $(this).text($(this).data('amount-without-nds'));
        }
    });
});

$(document).on('click', '.pop-click', function(e) {
    const title = $(this).data('title');
    $('.popover').each(function() {
        const h3 = $(this).find('h3').first();

        if (h3.text() !== title) {
            $(this).remove();
        }
    });
});
