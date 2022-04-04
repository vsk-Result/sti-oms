@extends('layouts.app')

@section('toolbar-title', 'Главная')
@section('breadcrumbs', Breadcrumbs::render('home'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card mb-5">
            <div class="card-body py-3">
                <p>Добро пожаловать!</p>
            </div>
        </div>

        @can('index crm-costs')
            <div class="row">
            <div class="col-md-6">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header">
                        <div class="card-title">
                            Кассы с проблемными закрытыми месяцами
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-125px">Касса</th>
                                <th class="min-w-125px">Не закрыто</th>
                                <th class="min-w-125px">Не разбито</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach($closures as $closureId => $closure)
                                    <tr>
                                        <td>
                                            <a target="_blank" href="http://crm.local/costs/{{ $closureId }}">{{ $closure['name'] }}</a>
                                        </td>
                                        <td>
                                            @foreach($closure['not_close'] as $date)
                                                <a href="javascript:void(0);" class="badge badge-light bg-hover-light-primary fs-7 m-1 cursor-default">{{ $date }}</a>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($closure['not_split'] as $date)
                                                <a href="javascript:void(0);" class="badge badge-light bg-hover-light-primary fs-7 m-1 cursor-default">{{ $date }}</a>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>
@endsection
