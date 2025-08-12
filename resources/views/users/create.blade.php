@extends('layouts.app')

@section('title', 'Новый пользователь')
@section('toolbar-title', 'Новый пользователь')
@section('breadcrumbs', Breadcrumbs::render('users.create'))

@section('content')
    <div class="post" id="kt_post">
        <div class="container">
            @include('users.create-blocks.general')
        </div>
    </div>
@endsection
