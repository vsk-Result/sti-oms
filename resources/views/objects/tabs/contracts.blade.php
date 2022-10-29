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
        mainApp.initFreezeTable(2);
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
