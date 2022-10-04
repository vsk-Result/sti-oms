@extends('objects.layouts.show')

@section('object-tab-title', 'Гарантийные удержания')

@section('object-tab-content')
    @include('guarantees.modals.filter')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            @include('guarantees.partials._guarantees')
        </div>
    </div>
@endsection
