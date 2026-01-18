@extends('objects.layouts.show')

@section('object-tab-title', 'Отчет доходов и расходов')

@section('object-tab-content')
    @include('objects.modals.payment_receive_report_filter')

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">Отчет о доходах и расходах на {{ now()->format('d.m.Y') }} по объекту {{ $object->getName() }}</div>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#paymentReceiveReportFilterModal">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                                    </svg>
                                </span>
                                Фильтр
                            </button>

                            <form action="{{ route('objects.payment_receive_report.export.store', $object) . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
                                @csrf
                                <a
                                        href="javascript:void(0);"
                                        class="btn btn-light-success"
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
                    <div class="table-responsive freeze-table">
                        <table class="table table-bordered align-middle table-row-dashed fs-6 gy-3">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                                    <th class="ps-2" colspan="2">Отчет доходов и расходов</th>
                                    @foreach($reportInfo['monthsFull'] as $month)
                                        <th class="min-w-200px">{{ $month }}</th>
                                    @endforeach
                                    <th class="min-w-200px">Накопительные</th>
                                </tr>
                                <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                                    <th class="min-w-250px ps-2">Доходная часть</th>
                                    <th class="min-w-250px ps-2">Категория</th>
                                    @foreach($reportInfo['monthsFull'] as $month)
                                        <th class="min-w-200px">Сумма</th>
                                    @endforeach
                                    <th class="min-w-200px">Сумма</th>
                                </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold fs-7">
                                <tr>
                                    <td class="ps-5 fw-bolder">КС 2</td>
                                    <td>Материал</td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['material'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['material'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5"></td>
                                    <td>Работы</td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['rad'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['rad'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5"></td>
                                    <td>Накладные</td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['service'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['service'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr class="total-row">
                                    <td class="ps-5"></td>
                                    <td>Итого доходы: </td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['total'][$year][$month], 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['total'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5 fw-bolder">Расходная часть</td>
                                    <td></td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right"></td>
                                    @endforeach

                                    <td class="text-right"></td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Подрядчики</td>
                                    <td>Материал</td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['contractors']['material'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['contractors']['material'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5"></td>
                                    <td>Работы</td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['contractors']['rad'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['contractors']['rad'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Поставщики</td>
                                    <td>Материал</td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['providers']['material'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['providers']['material'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Услуги/накладные</td>
                                    <td>Содержание стройплащадки</td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['service']['service'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['service']['service'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Зарплата рабочие</td>
                                    <td></td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['salary_workers'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['salary_workers'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Зарплата ИТР</td>
                                    <td></td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['salary_itr'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['salary_itr'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Налоги с зп</td>
                                    <td></td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['salary_taxes'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['salary_taxes'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Услуги трансфера</td>
                                    <td></td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['transfer'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['transfer'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Общие затраты (в т.ч офис)</td>
                                    <td></td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['general_costs'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['general_costs'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr>
                                    <td class="ps-5">Налоги (НДС,прибыль)</td>
                                    <td></td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['accrued_taxes'][$year][$month] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['accrued_taxes'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr class="total-row">
                                    <td class="ps-5"></td>
                                    <td>Итого расходы: </td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['total'][$year][$month], 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['paymentInfo']['total'][$year]['total'], 'RUB', 0, true) }}</td>
                                </tr>

                                <tr class="total-row" style="background-color: #333">
                                    <td class="ps-5"></td>
                                    <td>Маржа: </td>

                                    @foreach($reportInfo['months'] as $month)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['total'][$year][$month] + $reportInfo['paymentInfo']['total'][$year][$month], 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($reportInfo['receiveInfo']['total'][$year]['total'] + $reportInfo['paymentInfo']['total'][$year]['total'], 'RUB', 0, true) }}</td>
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
            color: #3f4254;
        }

        .text-right {
            text-align: right !important;
        }

        .cell-center {
            vertical-align: middle !important;
            text-align: center !important;
        }

        .total-row {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });

        $(document).on('focus', '.db-field', function() {
            $(this).data('initial-amount', $(this).val());
        });

        $(document).on('blur', '.db-field', function() {
            const $that = $(this);
            const reason_id = $that.data('reason-id');
            const date = $that.data('date');
            const amount = $that.val();
            const url = $('.table').data('update-url');

            if ($that.data('initial-amount') !== amount) {
                mainApp.sendAJAX(
                    url,
                    'POST',
                    {
                        reason_id,
                        date,
                        amount,
                    }
                );
            }
        });

        $(document).on('click', '.collapse-trigger', function() {
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
        });

        $('#organization-select').select2({
            sorter: function(data) {
                return data.sort(function(a, b) {
                    return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                });
            },
            ajax: {
                url: '/organizations?type=select',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    const results = [];
                    $.each(data.organizations, function(id, text) {
                        results.push({id, text})
                    });
                    return {results};
                }
            }
        });
    </script>
@endpush
