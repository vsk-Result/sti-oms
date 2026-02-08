@extends('objects.layouts.show')

@section('object-tab-title', 'Cash Flow')

@section('object-tab-content')
    @include('objects.modals.cash_flow_add_payment')
    @include('objects.modals.cash_flow_payments')

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">Cash Flow</div>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button {{ $cashFlowPayments->count() === 0 ? 'disabled' : '' }} type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#cashFlowPaymentsModal">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                    </svg>
                                </span>
                                Расходы ({{ $cashFlowPayments->count() }})
                            </button>

                            <a href="#" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#cashFlowAddPaymentModal">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                    </svg>
                                </span>
                                Новый расход
                            </a>

                            <form action="{{ route('objects.receive_plan.exports.store', $object) }}" method="POST" class="hidden">
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
                        <table class="table table-bordered align-middle table-row-dashed fs-6 gy-3" data-update-url="{{ route('objects.receive_plan.store', $object) }}">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                                    <th class="min-w-400px ps-2">Основание</th>
                                    <th class="min-w-250px">Не оплачено с прошлого периода</th>

                                    @foreach($periods as $period)
                                        <th class="min-w-250px">{{ $period['format'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold fs-7">
                                <tr class="total-row">
                                    <td class="ps-2 fw-bolder">Поступления ИТОГО</td>
                                    <td></td>

                                    @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($plans->where('date', $period['start'])->sum('amount'), 'RUB') }}</td>
                                    @endforeach
                                </tr>
                                @foreach($reasons as $reasonId => $reason)
                                    <tr>
                                        <td class="ps-5">{{ $reason }}</td>
                                        <td></td>

                                        @foreach($periods as $period)
                                            @php
                                                $amount = $plans->where('date', $period['start'])->where('reason_id', $reasonId)->first()->amount;
                                                if ($amount == 0) {
                                                    $amount = '';
                                                }
                                            @endphp

                                            <td>
                                                <input
                                                    type="text"
                                                    value="{{ $amount }}"
                                                    class="amount-mask form-control form-control-sm form-control-solid db-field"
                                                    autocomplete="off"
                                                    data-reason-id="{{ $reasonId }}"
                                                    data-date="{{ $period['start'] }}"
                                                />
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr class="total-row">
                                    <td class="ps-2 fw-bolder">
                                        Расходы ИТОГО
                                    </td>

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['total']['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                    @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['total'][$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                <tr>
                                    <td class="ps-5">
                                        @if (count($cfPayments['details']['contractors']) > 0)
                                            <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="contractors-cf">+</span>
                                        @endif

                                        Работы
                                    </td>

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['contractors']['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['contractors'][$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                @foreach($cfPayments['details']['contractors'] as $contractorName => $info)
                                    <tr class="collapse-row" data-trigger="contractors-cf" style="display: none;">
                                        <td class="ps-10 fs-8 fst-italic">{{ $contractorName }}</td>

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                    @foreach($periods as $period)
                                            <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr>
                                    <td class="ps-5 fw-bold">Материалы</td>

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format(($cfPayments['providers_fix']['no_paid'] ?? 0) + ($cfPayments['providers_float']['no_paid'] ?? 0), 'RUB', 0, true) }}</td>


                                @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format(($cfPayments['providers_fix'][$period['start']] ?? 0) + ($cfPayments['providers_float'][$period['start']] ?? 0), 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                <tr>
                                    <td class="ps-9">
                                        @if (count($cfPayments['details']['providers_fix']) > 0)
                                            <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="providers-fix-cf">+</span>
                                        @endif

                                        Фиксированная часть
                                    </td>

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['providers_fix']['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['providers_fix'][$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                @foreach($cfPayments['details']['providers_fix'] as $contractorName => $info)
                                    <tr class="collapse-row" data-trigger="providers-fix-cf" style="display: none;">
                                        <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                    @foreach($periods as $period)
                                            <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr>
                                    <td class="ps-9">
                                        @if (count($cfPayments['details']['providers_float']) > 0)
                                            <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="providers-float-cf">+</span>
                                        @endif

                                        Изменяемая часть
                                    </td>

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['providers_float']['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['providers_float'][$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                @foreach($cfPayments['details']['providers_float'] as $contractorName => $info)
                                    <tr class="collapse-row" data-trigger="providers-float-cf" style="display: none;">
                                        <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                    @foreach($periods as $period)
                                            <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr>
                                    <td class="ps-5">
                                        @if (count($cfPayments['details']['service']) > 0)
                                            <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="service-cf">+</span>
                                        @endif

                                        Накладные/Услуги
                                    </td>

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['service']['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['service'][$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach
                                </tr>

                                @foreach($cfPayments['details']['service'] as $contractorName => $info)
                                    <tr class="collapse-row" data-trigger="service-cf" style="display: none;">
                                        <td class="ps-10 fs-8 fst-italic">{{ $contractorName }}</td>

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info['no_paid'] ?? 0, 'RUB', 0, true) }}</td>


                                    @foreach($periods as $period)
                                            <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr class="total-row">
                                    <td class="ps-2 fw-bolder">Сальдо ИТОГО</td>

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($cfPayments['total']['no_paid'] ?? 0, 'RUB', 0, true) }}</td>

                                    @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($plans->where('date', $period['start'])->sum('amount') + ($cfPayments['total'][$period['start']] ?? 0), 'RUB', 0, true) }}</td>
                                    @endforeach
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

        $(document).on('click','.collapse-trigger', function() {
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
