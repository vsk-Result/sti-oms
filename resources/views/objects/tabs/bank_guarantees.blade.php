@extends('objects.layouts.show')

@section('object-tab-title', 'Банковские гарантии')

@section('object-tab-content')
    @include('bank-guarantees.modals.filter')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            @include('bank-guarantees.partials._guarantees')
        </div>
    </div>
@endsection
