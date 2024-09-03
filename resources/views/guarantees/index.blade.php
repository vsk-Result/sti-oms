@extends('layouts.app')

@section('title', 'Гарантийные удержания')
@section('toolbar-title', 'Гарантийные удержания')
@section('breadcrumbs', Breadcrumbs::render('guarantees.index'))

@section('content')
    @include('guarantees.modals.filter')
    <div class="post">
        @include('guarantees.partials._guarantees')
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);
        });
    </script>
@endpush
