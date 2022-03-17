@extends('objects.layouts.show')

@section('object-tab-title', 'Оплаты')

@section('object-tab-content')
    @include('payments.modals.filter')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-body p-9 pt-6">
                    <div>
                        @foreach($years as $year)
                            <button data-year="{{ $year }}" type="button" class="cash-year btn btn-light {{ $year == request()->input('year', '') ? 'active' : '' }}">{{ $year }}</button>
                        @endforeach
                    </div>

                    <div class="mt-3 mb-3">
                        @foreach($months as $m => $month)
                            <button data-month="{{ $m }}" type="button" class="cash-month btn btn-light mt-1 {{ $m == request()->input('month', '') ? 'active' : '' }}">{{ $month }}</button>
                        @endforeach
                    </div>

                    <div
                        id="cash-payment-container"
                        data-index-payments-url="{{ route('objects.cash_payments.index', $object) }}"
                        data-object-id="{{ $object->id }}"
                        data-payment-type-id="{{ \App\Models\Payment::PAYMENT_TYPE_CASH }}"
                    ></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const cashPaymentsContainerblockUI = new KTBlockUI($('#cash-payment-container').get(0), {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Загрузка оплат...</div>',
        });

        let currentMonth;
        const $cashPaymentContainer = $('#cash-payment-container');

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
                            objects: $('#filter-object').val()
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

            const u = new URL(location.href);
            u.searchParams.set('object_id[]', $cashPaymentContainer.data('object-id'));
            u.searchParams.set('payment_type_id[]', $cashPaymentContainer.data('payment-type-id'));
            window.history.pushState('', '', u.href);

            const sortByField = u.searchParams.get('sort_by');
            const sortByDirection = u.searchParams.get('sort_direction');

            if (sortByField && sortByDirection) {
                const sortRow = $('th[data-sort-by=' + sortByField + ']');
                sortRow.removeClass('sorting-asc').removeClass('sorting-desc');
                sortRow.addClass('sorting-' + sortByDirection);
            }

            if ($('.cash-year.active').length === 0) {
                $('.cash-year:first-child').trigger('click');
            } else {
                currentMonth = $('.cash-month.active').data('month').toString();
                $('.cash-year.active').trigger('click');
            }
        });

        $(document).on('click', '.sortable-row', function(e) {
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

        $(document).on('click', '#export-store-form-submit', function(e) {
            e.preventDefault();
            const u = new URL(location.href);

            $('#export-store-form').attr('action', $('#export-store-form').data('export-store-url') + u.search);
            $('#export-store-form').submit();
        });

        $(document).on('click', '#filter-payment-submit', function(e) {
            e.preventDefault();
            const $form = $(this).closest('form');

            $form.attr('action', location.href);
            $form.submit();
        });

        $('.cash-year').on('click', function() {
            $('.cash-year').removeClass('active');
            $(this).addClass('active');

            loadMonths();
        });

        $('.cash-month').on('click', function() {
            $('.cash-month').removeClass('active');
            $(this).addClass('active');

            currentMonth = $(this).data('month').toString();

            loadPayments();
        });

        function loadMonths() {
            const url = $cashPaymentContainer.data('index-payments-url') + '?get_type=years';
            const year = $('.cash-year.active').data('year');

            $('.cash-month').removeClass('disabled').removeClass('active');

            mainApp.sendAJAX(
                url,
                'GET',
                {year},
                (data) => {
                    $('.cash-month').each(function() {
                        if (data.months.indexOf($(this).data('month').toString()) === -1) {
                            $(this).addClass('disabled');
                        }
                    });
                },
                {},
                () => {
                    $('.cash-month').each(function() {
                        if (currentMonth === $(this).data('month').toString() && ! $(this).hasClass('disabled')) {
                            $(this).trigger('click');
                        }
                    });

                    if ($('.cash-month.active').length === 0) {
                        $('.cash-month').not('.disabled').first().trigger('click');
                    }
                }
            );
        }

        function loadPayments() {
            cashPaymentsContainerblockUI.block();
            const url = $cashPaymentContainer.data('index-payments-url');
            const year = $('.cash-year.active').data('year');
            const month = $('.cash-month.active').data('month');

            $('#filterPaymentModal input[name=year]').val(year);
            $('#filterPaymentModal input[name=month]').val(month);

            const u = new URL(location.href);
            u.searchParams.set('year', year);
            u.searchParams.set('month', month);
            window.history.pushState('', '', u.href);

            mainApp.sendAJAX(
                url + u.search,
                'GET',
                {},
                (data) => {
                    $cashPaymentContainer.html(data.payments_view);
                },
                {},
                () => {
                    cashPaymentsContainerblockUI.release();
                }
            );
        }
    </script>
@endpush
