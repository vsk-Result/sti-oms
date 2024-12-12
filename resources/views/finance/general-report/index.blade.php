@extends('layouts.app')

@section('title', 'Отчет по общим затратам')
@section('toolbar-title', 'Отчет по общим затратам')
@section('breadcrumbs', Breadcrumbs::render('general_report.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title"></div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <form action="{{ route('general_report.exports.store') }}" method="POST" class="hidden">
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
            <div class="card-body pt-0">
                    <div class="table-responsive freeze-table">
                    <table class="table table-bordered align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0 cell-center">
                                <th class="min-w-150px ps-2" rowspan="2" colspan="2">Категория</th>
                                <th rowspan="2" class="collapse-col">Статья затрат/поступлений</th>
                                <th rowspan="2" class="total-column hl">Итого</th>
                                <th colspan="{{ count($years) }}">Сумма</th>
                            </tr>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                @foreach($years as $year)
                                    <th class="min-w-150px text-center">{{ $year }}</th>
                                @endforeach
                            </tr>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th colspan="3" class="ps-2 hl total-cell">ИТОГО</th>
                                @php
                                    $totalAll = 0;
                                    $totalByYears = [];
                                    foreach ($items as $categoryItem) {
                                        $totalAll += $categoryItem['amount'];

                                        foreach ($years as $year) {
                                            if (!isset($totalByYears[$year])) {
                                                $totalByYears[$year] = 0;
                                            }
                                            $totalByYears[$year] += $categoryItem['years'][$year];
                                        }
                                    }
                                @endphp
                                <th class="hl text-right {{ $totalAll > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($totalAll, 'RUB', 0, true) }}</th>
                                @foreach($years as $year)
                                    <th class="min-w-150px text-right hl {{ $totalByYears[$year] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($totalByYears[$year], 'RUB', 0, true) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold fs-7">
                            @foreach($items as $categoryItem)
                                @php
                                    $totalReceive = 0;
                                    $totalReceiveByYear = [];
                                    $totalPay = 0;
                                    $totalPayByYear = [];

                                    foreach ($years as $year) {
                                         $totalReceiveByYear[$year] = 0;
                                         $totalPayByYear[$year] = 0;
                                    }


                                    foreach ($categoryItem['codes']['receive'] as $codeItem) {
                                        $totalReceive += $codeItem['amount'];

                                        foreach ($years as $year) {
                                            $totalReceiveByYear[$year] += $codeItem['years'][$year];
                                        }
                                    }
                                    foreach ($categoryItem['codes']['pay'] as $codeItem) {
                                        $totalPay += $codeItem['amount'];

                                        foreach ($years as $year) {
                                            $totalPayByYear[$year] += $codeItem['years'][$year];
                                        }
                                    }
                                @endphp

                                <tr class="bg-category">
                                    <td class="ps-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="{{ $categoryItem['name'] }}">+</td>
                                    <td class="ps-2">{{ $categoryItem['name'] }}</td>
                                    <td class="collapse-col"></td>
                                    <td class="hl text-right {{ $categoryItem['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($categoryItem['amount'], 'RUB', 0, true) }}</td>
                                    @foreach($years as $year)
                                        <td class="hl text-right {{$categoryItem['years'][$year] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($categoryItem['years'][$year], 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                <tr class="collapse-row fw-bolder" data-trigger="{{ $categoryItem['name'] }}" style="display: none;">
                                    <td colspan="2" class="ps-2">Приходы</td>
                                    <td class="collapse-col">Итого</td>
                                    <td class="hl text-right {{$totalReceive > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($totalReceive, 'RUB', 0, true) }}</td>
                                    @foreach($years as $year)
                                        <td class="hl text-right {{$totalReceiveByYear[$year] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($totalReceiveByYear[$year], 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                @foreach($categoryItem['codes']['receive'] as $codeItem)
                                    <tr class="collapse-row" data-trigger="{{ $categoryItem['name'] }}" style="display: none;">
                                        <td colspan="2"></td>
                                        <td class="collapse-col"><a target="_blank" href="{{ route('payments.index') . '?code%5B%5D=' . $codeItem['code'] }}">{{ $codeItem['name'] }}</a></td>
                                        <td class="hl text-right {{ $codeItem['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($codeItem['amount'], 'RUB', 0, true) }}</td>
                                        @foreach($years as $year)
                                            <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($codeItem['years'][$year], 'RUB', 0, true) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr class="collapse-row fw-bolder" data-trigger="{{ $categoryItem['name'] }}" style="display: none;">
                                    <td colspan="2" class="ps-2">Расходы</td>
                                    <td class="collapse-col">Итого</td>
                                    <td class="hl text-right {{$totalPay > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($totalPay, 'RUB', 0, true) }}</td>
                                    @foreach($years as $year)
                                        <td class="hl text-right {{$totalPayByYear[$year] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($totalPayByYear[$year], 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                @foreach($categoryItem['codes']['pay'] as $codeItem)
                                    <tr class="collapse-row" data-trigger="{{ $categoryItem['name'] }}" style="display: none;">
                                        <td colspan="2"></td>
                                        <td class="collapse-col"><a target="_blank" href="{{ route('payments.index') . '?code%5B%5D=' . $codeItem['code'] }}">{{ $codeItem['name'] }}</a></td>
                                        <td class="hl text-right {{ $codeItem['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($codeItem['amount'], 'RUB', 0, true) }}</td>
                                        @foreach($years as $year)
                                            <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($codeItem['years'][$year], 'RUB', 0, true) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);
            checkHideColumn();
        });

        $('.collapse-trigger').on('click', function() {
            const $tr = $(this);
            const trigger = $tr.data('trigger');
            const isCollapsed = $tr.hasClass('collapsed');

            if (isCollapsed) {
                $tr.text('+');
                $tr.removeClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).hide();
            } else {
                $tr.text('-');
                $tr.addClass('collapsed');
                $(`.collapse-row[data-trigger="${trigger}"]`).show();
            }

            checkHideColumn();
        })

        function checkHideColumn() {
            const collapsedCategoriesCount = $('.collapse-trigger.collapsed').length;

            if (collapsedCategoriesCount > 0) {
                $('.total-cell').attr('colspan', 3);
                $('.collapse-col').show();
            } else {
                $('.total-cell').attr('colspan', 2);
                $('.collapse-col').hide();
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .table td, .table th {
            border: 1px solid #c8c8c8 !important;
        }
        .table td:not(.text-danger), .table td:not(.text-success), .table th {
            color: #181c32;
        }
        .hl:not(.bg-category), .table tbody tr:last-child td.hl {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
            min-width: 150px !important;
        }

        .text-right {
            text-align: right !important;
        }

        .total-column {
            min-width: 150px !important;
            width: 150px !important;
            max-width: 150px !important;
        }
        
        .bg-category, .bg-category td.hl {
            background-color: #dfecfb !important;
        }

        .cell-center {
            vertical-align: middle !important;
            text-align: center !important;
        }
    </style>
@endpush
