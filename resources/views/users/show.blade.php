@extends('layouts.app')

@section('toolbar-title', 'Аккаунт')
@section('breadcrumbs', Breadcrumbs::render('users.show', auth()->user()))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Основное</h3>
                    </div>
                </div>
                <div>
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Фотография</label>
                            <div class="col-lg-8">
                                <div class="image-input image-input-outline" data-kt-image-input="true">
                                    <a target="_blank" href="{{ $user->getPhoto() }}">
                                        <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $user->getPhoto() }})"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-bold fs-6">Имя Фамилия</label>
                            <div class="col-lg-8 fv-row">
                                <label class="col-form-label fw-bold fs-6 text-gray-600">{{ $user->name }}</label>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Email</label>
                            <div class="col-lg-8 fv-row">
                                <label class="col-form-label fw-bold fs-6 text-gray-600">
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </label>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Номер телефона</label>
                            <div class="col-lg-8 fv-row">
                                <label class="col-form-label fw-bold fs-6 text-gray-600">{{ $user->phone }}</label>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">Статус</label>
                            <div class="col-lg-8 fv-row">
                                @include('partials.status', ['status' => $user->getStatus()])
                            </div>
                        </div>
                    </div>
                    @can('edit admin-users')
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Изменить</a>
                        </div>
                    @endcan
                </div>
            </div>

            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0">
                    <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Последние 10 сеансов входа в систему</h3>
                    </div>
                </div>
                <div>
                    <div class="card-body border-top p-9">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <tr class="text-start text-muted text-uppercase gs-0">
                                        <th>IP адрес</th>
                                        <th>Успешный вход</th>
                                        <th>Время входа</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600">
                                    @foreach($user->authentications->take(10) as $auth)
                                        <tr>
                                            <td>{{ $auth->ip_address }}</td>
                                            <td>{{ $auth->login_successful ? 'Да' : 'Нет' }}</td>
                                            <td>
                                                @if ($auth->login_at)
                                                    {{ $auth->login_at->diffForHumans() }}
                                                    <br>
                                                    <span class="fs-7 text-muted">({{ $auth->login_at->format('d.m.Y H:i') }})</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
