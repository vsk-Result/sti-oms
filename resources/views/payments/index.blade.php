@extends('layouts.app')

@section('title', 'Оплаты')
@section('toolbar-title', 'Оплаты')
@section('breadcrumbs', Breadcrumbs::render('payments.index'))

@section('content')
    @include('payments.modals.filter')
    @include('payments.modals.create')
    @include('payments.modals.edit')

    <div class="post">
        @include('payments.parts._payments')
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#organization-select').select2({
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                    });
                },
                ajax: {
                    url: '/organizations?type=select',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                        };
                    },
                    processResults: function (data) {
                        const results = [];
                        $.each(data.organizations, function(id, text) {
                            results.push({id, text})
                        });
                        return {results};
                    }
                }
            });

            const url = new URL(document.location.href);
            const sortByField = url.searchParams.get('sort_by');
            const sortByDirection = url.searchParams.get('sort_direction');

            if (sortByField && sortByDirection) {
                const sortRow = $('th[data-sort-by=' + sortByField + ']');
                sortRow.removeClass('sorting-asc').removeClass('sorting-desc');
                sortRow.addClass('sorting-' + sortByDirection);
            }
        });

        $('.sortable-row').on('click', function(e) {
            e.preventDefault();
            const field = $(this).data('sort-by');
            const url = new URL(document.location.href);

            if (url.searchParams.has('sort_by')) {
               url.searchParams.set('sort_by', field);
            } else {
               url.searchParams.append('sort_by', field);
            }

            if (url.searchParams.has('sort_direction')) {
                url.searchParams.set('sort_direction', url.searchParams.get('sort_direction') === 'asc' ? 'desc' : 'asc');
            } else {
                url.searchParams.append('sort_direction', 'asc');
            }

            document.location = url.toString();
        });

        $('.payment-fill-row').on('click', function() {
            const $that = $(this);
            const $tr = $that.closest('tr');
            const color = $tr.css('color');

            $tr.css('cssText', '--bs-table-accent-bg: ' + $that.data('color') + ';background-color: ' + $that.data('color') + ' !important; color: ' + color + ' !important');
            updatePayment($tr, 'parameters', 'transfer_background_color::' + $that.data('color'));
        });

        $('.payment-fill-color').on('click', function() {
            const $that = $(this);
            const $tr = $that.closest('tr');
            const bgColor = $tr.css('background-color');

            $tr.css('cssText', '--bs-table-accent-bg: ' + bgColor + ';background-color: ' + bgColor + ' !important; color: ' + $that.data('color') + ' !important');
            updatePayment($tr, 'parameters', 'transfer_font_color::' + $that.data('color'));
        });

        function updatePayment($row, field, value) {
            const url =  $row.data('payment-update-url');

            mainApp.sendAJAX(
                url,
                'POST',
                {[field]: value}
            );
        }

        $('.create-payment').on('click', function() {
            $('#createPaymentModal .modal-content').html('');
            const url = $(this).data('create-payment-url');
            mainApp.sendAJAX(
                url,
                'GET',
                {},
                (data) => {
                    $('#createPaymentModal .modal-content').html(data.payment_form);
                },
                {},
                () => {
                    KTApp.init();
                    mainApp.init();
                    $('[name=return_url]').val(window.location.href);
                    $('#createPaymentModal').modal('show');
                }
            );
        });

        $('.copy-payment').on('click', function() {
            if (confirm('Вы действительно создать оплату на основе данной?')) {
                $('#createPaymentModal .modal-content').html('');
                const url = $(this).data('create-payment-url');
                mainApp.sendAJAX(
                    url,
                    'GET',
                    {},
                    (data) => {
                        $('#createPaymentModal .modal-content').html(data.payment_form);
                    },
                    {},
                    () => {
                        KTApp.init();
                        mainApp.init();
                        $('[name=return_url]').val(window.location.href);
                        $('#createPaymentModal').modal('show');
                    }
                );
            }
        });

        $('.edit-payment').on('click', function() {
            $('#editPaymentModal .modal-content').html('');
            const url = $(this).data('edit-payment-url');
            mainApp.sendAJAX(
                url,
                'GET',
                {},
                (data) => {
                    $('#editPaymentModal .modal-content').html(data.payment_form);
                },
                {},
                () => {
                    KTApp.init();
                    mainApp.init();
                    $('[name=return_url]').val(window.location.href);
                    $('#editPaymentModal').modal('show');
                }
            );
        });

        $('.amount-expression-quick').on('click', function() {
            $('select[name=amount_expression_operator]').val($(this).data('operator')).trigger('change');
            $('input[name=amount_expression]').val(0);

            if ($(this).is('[data-operator-force]')) {
                $('#filter-payment-submit').trigger('click');
            }
        });

        $('.period-quick').on('click', function() {
            const year = $(this).text();
            $('input[name=period]').val('01.01.' + year + ' - 31.12.' + year);
        });

        $('.description-or').on('click', function() {
            const $input = $('input[name=description]');
            $input .val($input.val() + '%%');
        });

        $('.description-and').on('click', function() {
            const $input = $('input[name=description]');
            $input .val($input.val() + '^^');
        });

        $("#payment-filter-code").on("select2:selecting", function(evt) {
            const code = evt.params.args.data.id;
            const selectedItems = $(this).select2("val");

            $(this).find(`[data-parent-1="${code}"]`).each(function() {
                selectedItems.push($(this).attr('value'));
            });

            $(this).find(`[data-parent-2="${code}"]`).each(function() {
                selectedItems.push($(this).attr('value'));
            });

            $(this).val(selectedItems).trigger('change');
        });

        mainApp.initFreezeTable(1);
    </script>
@endpush

