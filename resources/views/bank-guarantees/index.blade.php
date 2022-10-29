@extends('layouts.app')

@section('toolbar-title', 'Банковские гарантии и депозиты')
@section('breadcrumbs', Breadcrumbs::render('bank_guarantees.index'))

@section('content')
    @include('bank-guarantees.modals.filter')
    <div class="post">
        @include('bank-guarantees.partials._guarantees')
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);
        });
    </script>
@endpush
