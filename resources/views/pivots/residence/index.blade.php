@extends('layouts.app')

@section('title', 'Отчет о проживании')
@section('toolbar-title', 'Отчет о проживании')
@section('breadcrumbs', Breadcrumbs::render('pivots.residence.index'))

@section('content')
    <div class="post">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет о проживании</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <form class="form" action="{{ route('pivots.residence.exports.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-12 fv-row mb-5">
                                    <label class="fs-5 fw-bold mb-2">Дата</label>
                                    @php
                                        $months = [];
                                        foreach (['2025', '2024', '2023', '2022', '2021'] as $year) {
                                            foreach (['Декабрь', 'Ноябрь', 'Октябрь', 'Сентябрь', 'Август', 'Июль', 'Июнь', 'Май', 'Апрель', 'Март', 'Февраль', 'Январь'] as $m) {
                                                $months[] = $m . ' ' . $year;
                                            }
                                        }
                                    @endphp

                                    <select name="date" data-control="select2" class="form-select form-select-solid form-select-lg">
                                        @foreach($months as $month)
                                            <option value="{{ $month }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-12 fv-row mb-5">
                                    <label class="fs-5 fw-bold mb-2">Общежитие</label>
                                    <select name="dormitory_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                        @foreach($dormitories as $dormitory)
                                            <option value="{{ $dormitory['Id'] }}">{{ $dormitory['Name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex flex-center py-3">
                                    <button type="submit" class="btn btn-primary me-3">
                                        <span class="indicator-label">Сформировать</span>
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
