@extends('layouts.app')

@section('toolbar-title', 'Доступ к объектам')
@section('breadcrumbs', Breadcrumbs::render('object_users.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card">
            <div class="card-body pt-0">
                <table class="table table-hover align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="min-w-125px">Объект</th>
                        <th class="min-w-125px">Пользователи</th>
                        <th class="text-end min-w-100px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($objects as $object)
                            <tr>
                                <td>{{ $object->getName() }}</td>
                                <td>
                                    @foreach($object->users as $user)
                                        <a href="{{ route('users.show', $user) }}" class="badge badge-light bg-hover-light-primary fs-7 m-1">{{ $user->name }}</a>
                                    @endforeach
                                </td>
                                <td class="text-end">
                                    @can('edit admin-roles')
                                        <a href="{{ route('objects.users.edit', $object) }}" class="btn btn-light btn-active-color-primary btn-sm">Изменить</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
