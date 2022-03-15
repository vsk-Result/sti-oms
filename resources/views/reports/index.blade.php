@extends('layouts.app')

@section('toolbar-title', 'Отчеты')
@section('breadcrumbs', Breadcrumbs::render('reports.index'))

@section('content')
    <div class="post">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет по расходам на ЗП ИТР по проектам</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            @if ($errors->any())
                                <div class="alert alert-dismissible bg-light-danger border border-dashed border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                                    <div class="d-flex flex-column pe-0 pe-sm-10">
                                        <h5 class="mb-1">Ошибки при загрузке</h5>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <form class="form" action="{{ route('reports.itr_salary_object.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-12 fv-row mb-5">
                                    <label class="required fs-5 fw-bold mb-2">Файл для загрузки</label>
                                    <input type="file" class="form-control form-control-solid" placeholder="" name="file" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                                    <div class="form-text">Доступные форматы:
                                        <code>xls, xlsx</code>
                                    </div>
                                </div>

                                <div class="d-flex flex-center py-3">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <span class="indicator-label">Загрузить</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Анализ оплат на предмет вычета НДС</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <form class="form" action="{{ route('reports.payment_nds_analyze.store') }}" method="POST">
                                @csrf

                                <div class="d-flex flex-center py-3">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <span class="indicator-label">Сформировать</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @can('index payments')
                    <div class="col-md-4">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder fs-3 mb-1">Разбить проживание</span>
                                </h3>
                            </div>
                            <div class="card-body py-3">
                                <form class="form" action="{{ route('payments.split_residence.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="col-md-12 fv-row mb-5">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Месяц</label>
                                            <div class="position-relative mb-3">
                                                @php
                                                    $months = [];
                                                    foreach (['2022', '2021'] as $year) {
                                                        foreach (['Декабрь', 'Ноябрь', 'Октябрь', 'Сентябрь', 'Август', 'Июль', 'Июнь', 'Май', 'Апрель', 'Март', 'Февраль', 'Январь'] as $m) {
                                                            $months[] = $m . ' ' . $year;
                                                        }
                                                    }
                                                @endphp

                                                <select name="month" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($months as $month)
                                                        <option value="{{ $month }}">{{ $month }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-center py-3">
                                        <button type="submit" class="btn btn-primary me-3">
                                            <span class="indicator-label">Загрузить</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endcan

                @if (auth()->user()->hasRole('super-admin'))
                    <div class="col-md-4">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder fs-3 mb-1">Загрузка договоров</span>
                                </h3>
                            </div>
                            <div class="card-body py-3">
                                <form class="form" action="{{ route('contracts.import.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="col-md-12 fv-row mb-5">
                                        <label class="required fs-5 fw-bold mb-2">Файл для загрузки</label>
                                        <input type="file" class="form-control form-control-solid" placeholder="" name="file" />
                                    </div>

                                    <div class="d-flex flex-center py-3">
                                        <button type="submit" class="btn btn-primary me-3">
                                            <span class="indicator-label">Загрузить</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder fs-3 mb-1">Обновление оплат</span>
                                </h3>
                            </div>
                            <div class="card-body py-3">
                                <form class="form" action="{{ route('reports.update_payments.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="col-md-12 fv-row mb-5">
                                        <label class="required fs-5 fw-bold mb-2">Файл для загрузки</label>
                                        <input type="file" class="form-control form-control-solid" placeholder="" name="file" />
                                    </div>

                                    <div class="d-flex flex-center py-3">
                                        <button type="submit" class="btn btn-primary me-3">
                                            <span class="indicator-label">Загрузить</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
