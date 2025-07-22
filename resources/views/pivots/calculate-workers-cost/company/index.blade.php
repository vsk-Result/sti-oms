@extends('layouts.app')

@section('title', 'Расчет стоимости рабочих')
@section('toolbar-title', 'Расчет стоимости рабочих')
@section('breadcrumbs', Breadcrumbs::render('pivots.calculate_workers_cost.index'))

@section('content')
    @include('pivots.calculate-workers-cost.modals.filter')
    @include('pivots.calculate-workers-cost.modals.update_itr_salary')
    @include('pivots.calculate-workers-cost.modals.update_ndfl')

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">Расчет стоимости рабочих по компании за {{ request()->get('year', date('Y')) }} год</div>

                        <div class="card-toolbar">
                            <a
                                    class="btn btn-light-primary me-3"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateNDFLModal"
                            >
                                Обновить НДФЛ и взносы
                            </a>

                            <a
                                    class="btn btn-light-primary me-3"
                                    href="javascript:void(0);"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateITRSalaryModal"
                            >
                                Обновить расходы ИТР
                            </a>

                            <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterCalculateWorkersCostModal">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                                    </svg>
                                </span>
                                Фильтр
                            </button>

                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <form action="{{ route('pivots.calculate_workers_cost.exports.store') . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
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
                            </div>
                        </div>
                </div>

                <div class="card-body py-3">
                    @if (session()->has('import_itr_salary_status'))
                        <div class="alert alert-dismissible bg-light-{{ session()->get('import_itr_salary_status_color') }} border border-dashed border-{{ session()->get('import_itr_salary_status_color') }} d-flex flex-column flex-sm-row p-5">
                            <div class="d-flex flex-column">
                                <h5 class="mb-1">Информация о загрузке файла</h5>
                                <p class="mb-0">{{ session()->get('import_itr_salary_status') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="table-responsive freeze-table">
                        <table class="table table-bordered align-middle table-row-dashed fs-6 gy-3">
                            <thead>
                                <tr class="fw-bolder">
{{--                                    <th rowspan="2" valign="middle" class="min-w-50px ps-2 text-center">№</th>--}}
                                    <th rowspan="2" valign="middle" class="min-w-500px ps-2 text-center">Раздел</th>

                                    @foreach($info['years'] as $year => $quarts)
                                        <th class="text-center" colspan="8">{{ $year }}</th>
                                    @endforeach

                                    <th class="text-center total-cell fw-bolder" colspan="2">Итого</th>
                                </tr>

                                <tr class="fw-bolder">
                                    @foreach($info['years'] as $year => $quarts)
                                        @foreach($quarts as $quart => $dates)
                                            <th class="text-center min-w-150px">{{ $quart }}</th>
                                            <th class="text-center min-w-150px">Расчет по часу</th>
                                        @endforeach
                                    @endforeach

                                    <th class="text-center min-w-150px total-cell fw-bolder">Сумма</th>
                                    <th class="text-center min-w-150px total-cell fw-bolder">Расчет по часу</th>
                                </tr>
                            </thead>

                            <tbody class="fw-bold fs-7">
                                @foreach($info['data'] as $items)
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="ps-2 {{ str_starts_with($item['group'], '- ') ? 'ps-6' : '' }}">{{ $item['group'] }}</td>

                                            @foreach($item['quarts'] as $quart)
                                                <td class="text-end">{{ \App\Models\CurrencyExchangeRate::format($quart['amount'], 'RUB', 0, true) }}</td>
                                                <td class="text-end">{{ \App\Models\CurrencyExchangeRate::format($quart['rate'], 'RUB', 0, true) }}</td>
                                            @endforeach

                                            <td class="text-end total-cell">{{ \App\Models\CurrencyExchangeRate::format($item['total']['amount'], 'RUB', 0, true) }}</td>
                                            <td class="text-end pe-2 total-cell">{{ \App\Models\CurrencyExchangeRate::format($item['total']['rate'], 'RUB', 0, true) }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                <tr style="background-color: #f7f7f7;">
                                    <td class="ps-2 hl">Итого</td>

                                    @foreach($info['total']['amount'] as $year => $quarts)
                                        @foreach($quarts['quarts'] as $index => $amount)
                                            <td class="text-end hl">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
                                            <td class="text-end hl">{{ \App\Models\CurrencyExchangeRate::format($info['total']['rate'][$year]['quarts'][$index], 'RUB', 0, true) }}</td>
                                        @endforeach
                                    @endforeach

                                    <td class="text-end pe-2 total-cell fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($info['total']['total']['amount'], 'RUB', 0, true) }}</td>
                                    <td class="text-end pe-2 total-cell fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($info['total']['total']['rate'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr style="background-color: #f7f7f7;">
                                    <td class="ps-2 hl">Количество часов рабочих (по данным из CRM)</td>

                                    @foreach($info['rates'] as $quarts)
                                        @foreach($quarts['quarts'] as $rate)
                                            <td class="text-center hl" colspan="2">{{ number_format($rate, 0, '.', ' ') }}</td>
                                        @endforeach
                                    @endforeach

                                    <td class="text-center pe-2 total-cell fw-bolder" colspan="2">{{ number_format($info['total']['total']['hours'], 0, '.', ' ') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table td, .table th, .table tbody tr:last-child td {
            border: 1px solid #c8c8c8 !important;
        }

        .hl, .table tbody tr:last-child td.hl {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
            border: 1px dashed #ccc !important;
            min-width: 150px !important;
        }

        .table th.total-cell, .table td.total-cell {
            border: 1px solid #c8c8c8 !important;
            background-color: #eee !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });
    </script>
@endpush