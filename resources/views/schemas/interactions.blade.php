@extends('layouts.app')

@section('toolbar-title', 'Схема взаимодействия')
@section('breadcrumbs', Breadcrumbs::render('schemas.interactions.index'))

@section('content')
    <div class="interactions">
        {!! file_get_contents('images/schemas/interactions.svg') !!}
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // $('.interactions div:contains("Займ")').css('background-color', 'red');
        });
    </script>
@endpush
