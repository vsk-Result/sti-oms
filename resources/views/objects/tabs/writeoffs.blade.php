@extends('objects.layouts.show')

@section('object-tab-title', 'Списания')

@section('object-tab-content')
    @include('writeoffs.modals.filter')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            @include('writeoffs.parts._writeoffs')
        </div>
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
