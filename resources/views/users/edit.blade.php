@extends('layouts.app')

@section('title', 'Настройки аккаунта')
@section('toolbar-title', 'Настройки аккаунта')
@section('breadcrumbs', Breadcrumbs::render('users.edit', auth()->user()))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            @include('users.edit-blocks.general')
            @can('edit admin-users')
                @include('users.edit-blocks.roles')
                @include('users.edit-blocks.permissions')
            @endcan
        </div>
    </div>
@endsection
