@extends('layouts.app')

@section('title', 'Главная')
@section('toolbar-title', 'Главная')
@section('breadcrumbs', Breadcrumbs::render('home'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card mb-5">
            <div class="card-body py-3">
                <p>Добро пожаловать!</p>
            </div>
        </div>
    </div>
@endsection
