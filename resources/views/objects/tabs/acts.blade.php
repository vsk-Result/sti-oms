@extends('objects.layouts.show')

@section('object-tab-title', 'Акты')

@section('object-tab-content')
    @include('acts.modals.filter')
    @include('acts.modals.line_chart_payments')

    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            @include('acts.partials._acts')
        </div>
    </div>
@endsection
