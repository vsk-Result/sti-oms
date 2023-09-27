@extends('layouts.app')

@section('toolbar-title', 'Списания')
@section('breadcrumbs', Breadcrumbs::render('writeoffs.index'))

@section('content')
    @include('writeoffs.modals.filter')

    <div class="post">
        @include('writeoffs.parts._writeoffs')
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.period-quick').on('click', function () {
                const year = $(this).text();
                $('input[name=period]').val('01.01.' + year + ' - 31.12.' + year);
            });

            mainApp.initFreezeTable(1);
        });
    </script>
@endpush

