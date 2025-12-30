@extends('layouts.app')

@section('title', 'Свод отчетов')
@section('toolbar-title', 'Свод отчетов')
@section('breadcrumbs', Breadcrumbs::render('reports.all_reports.index'))

@section('content')
    <div class="post">
        <div class="content-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Финансовый отчет</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Сводные данные</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('finance_report.index') }}">Перейти к отчету</a></p>
                                <p><a class="fs-6 fw-bold" href="{{ route('files.download', ['file' => base64_encode('public/finance_report_example.xlsx'), 'name' => 'Пример финансового отчета']) }}">Скачать пример отчета</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет по общим затратам</span>
                                <span class="text-muted mt-1 fw-semibold fs-7">Распределение общих затрат между объектами</span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('general_report.index') }}">Перейти к отчету</a></p>
                                <p><a class="fs-6 fw-bold" href="{{ route('files.download', ['file' => base64_encode('public/general_report_example.xlsx'), 'name' => 'Пример отчета по общим затратам']) }}">Скачать пример отчета</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет CASH FLOW</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('pivots.cash_flow.index') }}">Перейти к отчету</a></p>
                                <p><a class="fs-6 fw-bold" href="{{ route('files.download', ['file' => base64_encode('public/cash_flow_report_example.xlsx'), 'name' => 'Пример отчета CASH FLOW']) }}">Скачать пример отчета</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет по категориям</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('pivots.acts_category.index') }}">Перейти к отчету</a></p>
                                <p><a class="fs-6 fw-bold" href="{{ route('files.download', ['file' => base64_encode('public/acts_category_report_example.xlsx'), 'name' => 'Пример отчета по категориям']) }}">Скачать пример отчета</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет по балансам</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('pivots.balances.index') }}">Перейти к отчету</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет о движении денежных средств</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('pivots.money_movement.index') }}">Перейти к отчету</a></p>
                                <p><a class="fs-6 fw-bold" href="{{ route('files.download', ['file' => base64_encode('public/money_movement_report_example.xlsx'), 'name' => 'Пример отчета о движении денежных средств']) }}">Скачать пример отчета</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет о проживании</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('pivots.residence.index') }}">Перейти к отчету</a></p>
                                <p><a class="fs-6 fw-bold" href="{{ route('files.download', ['file' => base64_encode('public/residence_report_example.xlsx'), 'name' => 'Пример отчета о проживании']) }}">Скачать пример отчета</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bolder fs-3 mb-1">Отчет о стоимости рабочих</span>
                                <span class="text-muted mt-1 fw-semibold fs-7"></span>
                            </h3>
                        </div>
                        <div class="card-body py-3">
                            <div class="col-md-12 fv-row mb-5">
                                <p> <a class="fs-6 fw-bold" href="{{ route('pivots.calculate_workers_cost.index') }}">Перейти к отчету</a></p>
                                <p><a class="fs-6 fw-bold" href="{{ route('files.download', ['file' => base64_encode('public/calculate_workers_cost_report_example.xlsx'), 'name' => 'Пример отчета о стоимости рабочих']) }}">Скачать пример отчета</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

