@extends('layouts.app')

@section('toolbar-title', 'Пользователи')
@section('breadcrumbs', Breadcrumbs::render('users.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Поиск" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">

                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="min-w-125px">Пользователь</th>
                        <th class="min-w-125px">Роль</th>
                        <th class="min-w-125px">Email проверка</th>
                        <th class="min-w-125px">Зарегистрирован</th>
                        <th class="min-w-125px">Статус</th>
                        <th class="text-end min-w-100px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($users as $user)
                            <tr>
                                <td class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3 cursor-pointer">
                                        <div class="symbol-label">
                                            <img src="{{ $user->getPhoto() }}" alt="{{ $user->name }}" class="w-100" />
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 mb-1">{{ $user->name }}</span>
                                        <a href="mailto:{{ $user->email }}"><span>{{ $user->email }}</span></a>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $roles = implode(', ', $user->roles->pluck('name')->toArray());
                                    @endphp
                                    {{ $roles }}
                                </td>
                                <td>
                                    @if($user->hasVerifiedEmail())
                                        <span class="badge badge-success fw-bolder">Подтвержден</span>
                                    @else
                                        <span class="badge badge-primary fw-bolder">Не подтвержден</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y H:i:s') }}</td>
                                <td>
                                    @include('partials.status', ['status' => $user->getStatus()])
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                        <span class="svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                            </svg>
                                        </span>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                        @can('show admin-users')
                                            <div class="menu-item px-3">
                                                <a href="{{ route('users.show', $user) }}" class="menu-link px-3">Посмотреть</a>
                                            </div>
                                        @endcan
                                        @can('edit admin-users')
                                            <div class="menu-item px-3">
                                                <a href="{{ route('users.edit', $user) }}" class="menu-link px-3">Изменить</a>
                                            </div>

                                            @if ($user->isDeleted())
                                                <div class="menu-item px-3">
                                                    <form action="{{ route('users.unblock', $user) }}" method="POST" class="hidden">
                                                        @csrf
                                                        <a
                                                            href="{{ route('users.unblock', $user) }}"
                                                            class="menu-link px-3"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите восстановить пользователя?')) {this.closest('form').submit();}"
                                                        >
                                                            Восстановить
                                                        </a>
                                                    </form>
                                                </div>
                                            @else
                                                @if ($user->isBlocked())
                                                    <div class="menu-item px-3">
                                                        <form action="{{ route('users.unblock', $user) }}" method="POST" class="hidden">
                                                            @csrf
                                                            <a
                                                                href="{{ route('users.unblock', $user) }}"
                                                                class="menu-link px-3"
                                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите разблокировать пользователя?')) {this.closest('form').submit();}"
                                                            >
                                                                Разблокировать
                                                            </a>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="menu-item px-3">
                                                        <form action="{{ route('users.block', $user) }}" method="POST" class="hidden">
                                                            @csrf
                                                            <a
                                                                href="{{ route('users.block', $user) }}"
                                                                class="menu-link px-3"
                                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите заблокировать пользователя?')) {this.closest('form').submit();}"
                                                            >
                                                                Заблокировать
                                                            </a>
                                                        </form>
                                                    </div>
                                                @endif

                                                <div class="menu-item px-3">
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                            href="{{ route('users.destroy', $user) }}"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить пользователя?')) {this.closest('form').submit();}"
                                                        >
                                                            Удалить
                                                        </a>
                                                    </form>
                                                </div>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
