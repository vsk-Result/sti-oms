@extends('layouts.app')

@section('toolbar-title', 'Финансовый отчет')
@section('breadcrumbs', Breadcrumbs::render('finance_report.index'))

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex flex-wrap flex-stack pb-7">
                <div class="d-flex flex-wrap" data-kt-user-table-toolbar="base">
                    <div class="me-3">
                        <span class="fs-5 fw-bold text-gray-600 pb-2 d-block">Выберите дату</span>
                        <input
                            readonly
                            type="text"
                            class="form-control form-control-solid date-range-picker-single"
                            name="date"
                            value="{{ $date->format('Y-m-d') }}"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-xxl-2 col-xxl-3">
            @include('finance-report.parts.account_balances')
            @include('finance-report.parts.deposites')
        </div>

        <div class="col-lg-6 col-md-6 col-xxl-5 col-xxl-4">
            @include('finance-report.parts.credits')
            @include('finance-report.parts.loans')
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @include('finance-report.parts.objects')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.applyBtn').on('click', function() {
                setTimeout(() => {
                    const url = new URL(document.location.href);
                    url.searchParams.set('balance_date', $('input[name=date]').val());
                    document.location = url.toString();
                }, 300);
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .objects-table td, .objects-table th, .table>:not(:last-child)>:last-child>* {
            border: 1px solid #f15a2238;
        }
        .table tbody tr:last-child td {
            border-bottom: 1px solid #f15a2238 !important;
        }
        .objects-table th {
            vertical-align: middle !important;
            text-align: center !important;
        }
        .bl {
            border-left: 1px dashed #f15a22 !important;
        }
        .br {
            border-right: 1px dashed #f15a22 !important;
        }

        .hl, .table tbody tr:last-child td.hl {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
            border: 1px dashed #f15a22 !important;
            min-width: 150px !important;
        }

        .text-right {
            text-align: right !important;
        }

        .col-object {
            color: white !important;
            background-color: #f15a22 !important;
            border-right: 1px solid white !important;
        }
    </style>
@endpush
