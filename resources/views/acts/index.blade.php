@extends('layouts.app')

@section('toolbar-title', 'Акты')
@section('breadcrumbs', Breadcrumbs::render('acts.index'))

@section('content')
    @include('acts.modals.filter')
    <div class="post">
        @include('acts.partials._acts')
    </div>
@endsection

