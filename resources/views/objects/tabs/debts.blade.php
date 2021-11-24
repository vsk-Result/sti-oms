@extends('objects.layouts.show')

@section('object-tab-title', 'Долги')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-6">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Долг за материалы</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>

                    <a href="#" class="btn btn-sm btn-light-primary align-self-center">Добавить долг за материалы</a>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Организация</th>
                            <th>Сумма</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td>ПМП Электро</td>
                                <td>14 193 417</td>
                                <td><a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a></td>
                            </tr>
                            <tr>
                                <td>Промышленная экология</td>
                                <td>166 000</td>
                                <td><a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a></td>
                            </tr>
                            <tr>
                                <td>Техбай</td>
                                <td>97 444</td>
                                <td><a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a></td>
                            </tr>
                            <tr>
                                <td>АЙМЕТРО</td>
                                <td>81 624</td>
                                <td><a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a></td>
                            </tr>
                            <tr>
                                <td>Техновиа</td>
                                <td>6 040</td>
                                <td><a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Долг подрядчикам</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>

                    <a href="#" class="btn btn-sm btn-light-primary align-self-center">Добавить долг подрядчикам</a>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Организация</th>
                            <th>Сумма</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td>ПМП Электро</td>
                                <td>13 622 886</td>
                                <td><a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
