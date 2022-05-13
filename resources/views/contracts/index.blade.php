@extends('layouts.app')

@section('toolbar-title', 'Договора')
@section('breadcrumbs', Breadcrumbs::render('contracts.index'))

@section('content')

    @include('contracts.modals.filter')
    @include('acts.modals.line_chart_payments')

    <div class="post">
        @include('contracts.parts._main_contracts')
    </div>
@endsection

@push('scripts')
    <script>
        // $("#tr-1").scroll(function() {
        //     $("#tr-2").scrollLeft($("#tr-1").scrollLeft());
        // });
        // $("#tr-2").scroll(function() {
        //     $("#tr-1").scrollLeft($("#tr-2").scrollLeft());
        // });

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
            color: #009ef7 !important;
            border-color: #009ef7 !important;
            background-color: #f1faff !important;
        }
        .subcontract-row:hover {
            --bs-table-accent-bg: #ffe4c4 !important;
        }
    </style>
@endpush

