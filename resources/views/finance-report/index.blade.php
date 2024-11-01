@extends('layouts.app')

@section('title', 'Финансовый отчет на ' . $date->format('d.m.Y'))
@section('toolbar-title', 'Финансовый отчет на ' . $date->format('d.m.Y'))
@section('breadcrumbs', Breadcrumbs::render('finance_report.index'))

@section('content')
    @include('sidebars.finance_report_legend')

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex flex-wrap flex-stack pb-7">
                <div class="d-flex flex-wrap" data-kt-user-table-toolbar="base">
                    <div class="me-3 d-flex flex-row gap-2">
{{--                        <span class="fs-5 fw-bold text-gray-600 pb-2 d-block">Выберите дату</span>--}}
{{--                        <input--}}
{{--                            readonly--}}
{{--                            type="text"--}}
{{--                            class="form-control form-control-solid date-range-picker-single mb-3"--}}
{{--                            name="date"--}}
{{--                            value="{{ $date->format('Y-m-d') }}"--}}
{{--                        />--}}

                        <form action="{{ route('finance_report.exports.store', $date->format('Y-m-d')) }}" method="POST" class="hidden">
                            @csrf
                            <a
                                href="javascript:void(0);"
                                class="btn btn-light-primary"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                </svg>
                            </span>
                                Экспорт в Excel
                            </a>
                        </form>

                        <a
                            href="{{ route('finance_report.history.index') }}"
                            class="btn btn-light-dark"
                        >История</a>
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

            mainApp.initFreezeTable(2);
        });
    </script>
@endpush

@push('styles')
    <style>
        .objects-table td, .objects-table th, .table>:not(:last-child)>:last-child>* {
            border: 1px solid #eff2f5;
        }
        .table tbody tr:last-child td {
            border-bottom: 1px solid #eff2f5 !important;
        }
        .objects-table th {
            vertical-align: middle !important;
            text-align: center !important;
        }
        .bl {
            border-left: 1px solid #e3e6e9 !important;
        }
        .br {
            border-right: 1px solid #e3e6e9 !important;
        }

        .hl, .table tbody tr:last-child td.hl {
            background-color: #f7f7f780 !important;
            font-weight: bold !important;
            border: 1px solid #e3e6e9 !important;
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
