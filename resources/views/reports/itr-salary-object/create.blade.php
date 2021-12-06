@extends('layouts.app')

@section('toolbar-title', 'Отчет по расходам на ЗП ИТР по проектам')
@section('breadcrumbs', Breadcrumbs::render('reports.itr_salary_object.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
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

                        <div class="col-md-6 fv-row">
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
                            <a href="{{ route('payment_imports.index') }}" class="btn btn-light">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
