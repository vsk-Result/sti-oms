@extends('layouts.app')

@section('toolbar-title', 'Депозиты')
@section('breadcrumbs', Breadcrumbs::render('deposits.index'))

@section('content')
    @include('deposits.modals.filter')
    <div class="post">
        @include('deposits.partials._deposits')
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);
        });
    </script>
@endpush
