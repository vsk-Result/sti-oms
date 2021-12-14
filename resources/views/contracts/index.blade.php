@extends('layouts.app')

@section('toolbar-title', 'Договора')
@section('breadcrumbs', Breadcrumbs::render('contracts.index'))

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
                                <th class="min-w-150px">Номер</th>
                                <th class="min-w-150px">Сумма</th>
                                <th class="min-w-150px">Сумма аванса</th>
                                <th class="min-w-150px">Сумма полученного аванса</th>
                                <th class="min-w-150px">Сумма аванса к получению</th>
                                <th class="min-w-150px">Выполнено по актам</th>
                                <th class="min-w-150px">Аванс удержан по актам</th>
                                <th class="min-w-150px">Депозит удержан по актам</th>
                                <th class="min-w-150px">К оплате по актам</th>
                                <th class="min-w-150px">Оплачено по актам</th>
                                <th class="min-w-150px">Сумма неоплаченных работ по актам</th>
                                <th class="min-w-150px rounded-end pe-4">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td>LG-05-21</td>
                                <td>886 522 662.42</td>
                                <td>312 331 156.50</td>
                                <td>0.00</td>
                                <td>190 843 125.01</td>
                                <td>10 284 829.07</td>
                                <td>140 308.45</td>
                                <td>44 088.92</td>
                                <td>10 100 431.70</td>
                                <td>10 100 431.70</td>
                                <td>0.00</td>
                                <td>
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

