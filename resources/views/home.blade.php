@extends('layouts.app')

@section('toolbar-title', 'Главная')
@section('breadcrumbs', Breadcrumbs::render('home'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body py-3">
                <span>Добро пожаловать!</span>
            </div>
        </div>
    </div>
@endsection
