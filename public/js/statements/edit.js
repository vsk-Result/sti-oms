let $selectedRow = null;

$(document).on('td select2:open', function() {
    document.querySelector('.select2-search__field').focus();
});

$(document).on('select2:clear', 'td select', function() {
    const $that = $(this);
    updatePayment($that.closest('tr'), $that.attr('name'), $that.val());
});

$(document).on('select2:select', 'td select', function() {
    const $that = $(this);
    const name = $that.attr('name');
    const $tr = $that.closest('tr');
    const $next = $tr.next().find('select[name=' + name + ']');

    updatePayment($tr, name, $that.val());

    if ($next && ($next.val() === '' || $next.val() == null)) {
        $next.select2('open');
    }

    if (name === 'object_id') {
        if ($('#filter-payment').prop('checked')) {
            $tr.hide();
        }
    }
});

$(document).on('click', '#filter-payment', function() {
    if ($(this).prop('checked')) {
        $('.table-payments tbody tr').each(function () {
            if ($(this).find('td:first-child select').first().val() != 0) {
                $(this).hide();
            }
        });
    } else {
        $('.table-payments tbody tr').each(function () {
            $(this).show();
        });
    }
});

$(document).on('click', '.clone-payment', function() {

    const $that = $(this);
    const url = $('.table-payments').data('payment-store-url');

    mainApp.sendAJAX(
        url,
        'POST',
        {'base_payment_id': $that.data('payment-id')},
        function(data) {
            $('select.form-select').select2('destroy');

            const $tr = $that.closest('tr');
            const $cloneTr = $tr.clone();

            $cloneTr.data('update-payment-url', data.payment.update_url);
            $cloneTr.find('.destroy-payment').data('payment-destroy-url', data.payment.destroy_url);
            $cloneTr.find('.clone-payment').data('payment-id', data.payment.id);
            $cloneTr.insertBefore($tr);

            $cloneTr.addClass('new-row');

            KTApp.initSelect2();

            $cloneTr.find('select').trigger('change');
        }
    )
});

$(document).on('click', '.split-payment', function() {
    updateCRMAvansesImportsList();
    $selectedRow = $(this);
});

$(document).on('click', '#split-payment-submit', function() {
    const url = $selectedRow.data('split-payment-url');
    $selectedRow = $selectedRow.closest('tr');

    mainApp.sendAJAX(
        url,
        'POST',
        {'crm_avans_import_id': $('#crm-avans-import-id').val()},
        function(data) {
            $('select.form-select').select2('destroy');
            $.each(data.view_render, (key, value) => {
                $(value).insertAfter($selectedRow)
            });
            $selectedRow.remove();
            KTApp.initSelect2();
        },
        null,
        function() {
            $('#splitPaymentModal').modal('hide');
        }
    )
});

$(document).on('click', '.destroy-payment', function() {
    if (confirm('Вы действительно хотите удалить запись об оплате?')) {
        const $that = $(this);
        const url = $that.data('payment-destroy-url');

        mainApp.sendAJAX(
            url,
            'DELETE',
            {},
            function() {
                const $tr = $that.closest('tr');
                $tr.find('select').select2('destroy');
                $tr.remove();
            }
        );
    }
});

$(document).on('focus', '.db-field', function() {
    $(this).data('initial-text', $(this).val());
});

$(document).on('focus', '.db-field[name=code]', function() {
    if (! $('#kt_explore').hasClass('drawer-on')) {
        $('#kt_explore_toggle').trigger('click');
    }
});

$(document).on('blur', '.db-field[name=code]', function() {
    if ($('#kt_explore').hasClass('drawer-on')) {
        $('#kt_explore_toggle').trigger('click');
    }
});

$(document).on('keyup', '.db-field', function(e) {
    const field = $(this).attr('name');
    if (e.keyCode === 13) {
        const $next = $(this).closest('tr').next().find('.db-field[name=' + field + ']');
        if ($next && ($next.val() === '' || $next.val() == null)) {
            $next.focus();
            return false;
        }
    }
    if (field === 'amount' || field === 'code') {
        $(this).val($(this).val().replace(/[^-.,0-9]/, ''));
        $(this).val($(this).val().replace(',', '.'));
    }
});

$(document).on('blur', '.db-field', function() {
    const $that = $(this);
    const field = $that.attr('name');
    const text = $that.val();

    if (field === 'amount') {
        if (text.indexOf('-') !== -1) {
            $that.removeClass('text-success').addClass('text-danger');
        } else {
            $that.removeClass('text-danger').addClass('text-success');
        }

        if (text === '') {
            $that.val('0.00');
        } else if (text.indexOf('.') === -1) {
            $that.val(text + '.00');
        }
    } else if (field === 'code') {
        if (text.indexOf(',') !== -1) {
            $that.val(text.replace(',', '.'));
        }
    }

    if ($that.data('initial-text') !== text) {
        updatePayment($that.closest('tr'), field, text);
    }
});

function updateCRMAvansesImportsList() {
    const $select = $('#crm-avans-import-id');
    const url = $('#splitPaymentModal').data('crm-avanses-imports-list-url');

    mainApp.sendAJAX(
        url,
        'GET',
        {},
        function(data) {
            const config = $select.data('select2').options.options;
            $select
                .select2('destroy')
                .html('')
                .append(
                    $.map(data.imports, (value, key) => "<option value=\"" + key + "\">" + value + "</option>")
                )
                .select2(config);
        },
        null,
        function() {
            $('#splitPaymentModal').modal('show');
        }
    );
}

function updatePayment($row, $field, $value) {
    const url =  $row.data('payment-update-url');

    mainApp.sendAJAX(
        url,
        'POST',
        {[$field]: $value}
    );
}
