@extends('layouts.app')

@section('title', 'Долги')
@section('toolbar-title', 'Экспорт долгов')
@section('breadcrumbs', Breadcrumbs::render('pivots.debts.index'))

@section('content')
<div class="post">
    <div class="content-fluid">
        <div class="row">
            <div class="col-md-6">
                @if (session()->has('task_in_progress'))
                    <div class="alert alert-dismissible bg-light-warning border border-dashed border-warning d-flex flex-column flex-sm-row p-5 mb-10">
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <p class="mb-0">Отчет с данными параметрами находится на стадии формирования. После завершения на почту придет файл с отчетом.</p>
                        </div>
                    </div>
                @endif

                @if (session()->has('task_created'))
                    <div class="alert alert-dismissible bg-light-success border border-dashed border-success d-flex flex-column flex-sm-row p-5 mb-10">
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <p class="mb-0">Система начала формировать отчет. По завершению вам на почту придет файл с отчетом. Можете продолжить пользоваться сайтом.</p>
                        </div>
                    </div>
                @endif

                <div class="card mb-5 mb-xl-8">
                    <div class="card-body py-3">
                        <form class="form" action="{{ route('pivots.all_debts.export') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label class="form-label">Статус объектов</label>
                                <select
                                        id="filter-object"
                                        name="status"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-hide-search="true"
                                >
                                    <option value="0" selected="" data-select2-id="1">Активные</option>
                                    <option value="1" data-select2-id="2">Закрытые</option>
                                </select>
                            </div>

                            <div class="d-flex py-3">
                                <button type="submit" class="btn btn-primary me-3">
                                    <span class="indicator-label">Экспорт в Excel</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
