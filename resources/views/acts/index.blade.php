@extends('layouts.app')

@section('toolbar-title', 'Акты')
@section('breadcrumbs', Breadcrumbs::render('acts.index'))

@section('content')
    <div class="post">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <input disabled type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Поиск" />
                    </div>
                </div>

            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Договор</th>
                            <th>Номер</th>
                            <th>Выполнено</th>
                            <th>Аванс удержан</th>
                            <th>Депозит удержан</th>
                            <th>К оплате</th>
                            <th>Оплачено</th>
                            <th>Сумма неоплаченных работ</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td class="min-w-150px">LG-05-21</td>
                                <td class="min-w-150px">190 843 125.01</td>
                                <td class="min-w-150px">10 284 829.07</td>
                                <td class="min-w-150px">140 308.45</td>
                                <td class="min-w-150px">44 088.92</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="min-w-150px">LG-05-21 Д/C №1</td>
                                <td class="min-w-150px">190 843 125.01</td>
                                <td class="min-w-150px">10 284 829.07</td>
                                <td class="min-w-150px">140 308.45</td>
                                <td class="min-w-150px">44 088.92</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

