@extends('objects.layouts.show')

@section('object-tab-title', 'Депозиты')

@section('object-tab-content')
    @include('deposits.modals.filter')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            @include('deposits.partials._deposits')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);
        });
    </script>
@endpush
