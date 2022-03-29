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

    return {
        init: function () {
            initAjaxSetup();
            initSetToastrOptions();
            initFavouriteLinksCreateHandler();
            initDateRangePicker();

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
