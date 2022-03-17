@extends('objects.layouts.show')

@section('object-tab-title', 'Оплаты')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-body p-9 pt-6">
                    <div>
                        @foreach($years as $y => $year)
                            <button data-year="{{ $y }}" type="button" class="cash-year btn btn-light">{{ $year }}</button>
                        @endforeach
                    </div>

                    <div class="mt-3">
                        @foreach($months as $m => $month)
                            <button data-month="{{ $m }}" type="button" class="cash-month btn btn-light">{{ $month }}</button>
                        @endforeach
                    </div>

                    <div id="cash-payment-container" data-index-payments-url="{{ route('objects.cash_payments.index', $object) }}"></div>
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

        $('.cash-year').on('click', function() {
            $('.cash-year').removeClass('active');
            $(this).addClass('active');

            if ($('.cash-month.active').length === 0) {
                $('.cash-month:first-child').addClass('active');
            }

            loadPayments();
        });

        $('.cash-month').on('click', function() {
            $('.cash-month').removeClass('active');
            $(this).addClass('active');

            if ($('.cash-year.active').length === 0) {
                $('.cash-year:first-child').addClass('active');
            }

            loadPayments();
        });

        function loadPayments() {
            cashPaymentsContainerblockUI.block();
            const url = $('#cash-payment-container').data('index-payments-url');
            const year = $('.cash-year.active').data('year');
            const month = $('.cash-month.active').data('month');

            mainApp.sendAJAX(
                url,
                'GET',
                {year, month},
                (data) => {
                    $('#cash-payment-container').html(data.payments_view)
                },
                {},
                () => {
                    cashPaymentsContainerblockUI.release();
                }
            );
        }
    </script>
@endpush
