@extends('layouts.app')

@section('title', 'Отчет CASH FLOW')
@section('toolbar-title', 'Отчет CASH FLOW')
@section('breadcrumbs', Breadcrumbs::render('pivots.cash_flow.index'))

@section('content')
    <div class="card mb-5 mb-xl-8 border-0">
        <div class="card-header border-0">
            <div class="card-title"></div>

            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('pivots.cash_flow.exports.store') }}" method="POST" class="hidden">
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

        <div class="card-body p-0 ps-0">
            <div class="table-responsive freeze-table">
                <table class="table table-bordered align-middle table-row-dashed fs-6 gy-3">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                            <th class="min-w-400px ps-2"></th>
                            @foreach($periods as $period)
                                <th class="min-w-250px">{{ $period['format'] }}</th>
                            @endforeach

                            <th class="min-w-250px">ИТОГО</th>
                        </tr>

                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 total-row">
                            <th class="min-w-400px ps-2">ПОСТУПЛЕНИЯ ИТОГО, в том числе:</th>

                            @php
                                $total = 0;
                            @endphp
                            @foreach($periods as $period)
                                @php
                                    $amount = $plans->where('date', $period['start'])->sum('amount');
                                    $total += $amount;
                                @endphp
                                <th class="min-w-250px text-right">
                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                </th>
                            @endforeach

                            <th class="min-w-250px text-right pe-2">
                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                            </th>
                        </tr>

                        <tr class="text-start text-muted fs-8 gs-0">
                            <th class="min-w-400px ps-8 fw-bolder">Целевые авансы</th>

                            @php
                                $total = 0;
                            @endphp
                            @foreach($periods as $period)
                                @php
                                    $amount = $plans->where('date', $period['start'])->where('reason_id', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
                                    $total += $amount;
                                @endphp
                                <th class="min-w-250px text-right fst-italic">
                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                </th>
                            @endforeach

                            <th class="min-w-250px text-right pe-2 fst-italic">
                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                            </th>
                        </tr>

                        <tr class="text-start text-muted fs-8 gs-0">
                            <th class="min-w-400px ps-8 fw-bolder">Прочие поступления</th>

                            @php
                                $total = 0;
                            @endphp
                            @foreach($periods as $period)
                                @php
                                    $amount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
                                    $total += $amount;
                                @endphp
                                <th class="min-w-250px text-right fst-italic">
                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                </th>
                            @endforeach

                            <th class="min-w-250px text-right pe-2 fst-italic">
                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-600 fw-bold fs-7">
                        @foreach($objects as $object)
                            @php
                                $total = 0;
                                foreach($periods as $period) {
                                     $total += $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
                                }

                                if ($total == 0) {
                                    continue;
                                }
                            @endphp
                            <tr class="object-row">
                                <td class="ps-2 fw-bolder">{{ $object->name }}</td>

                                @foreach($periods as $period)
                                    @php
                                        $amount = $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
                                    @endphp
                                    <td class="text-right fw-bolder">
                                        {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                    </td>
                                @endforeach

                                <td class="text-right fw-bolder pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                </td>
                            </tr>

                            @foreach($reasons as $reasonId => $reason)
                                @php
                                    $total = $plans->where('object_id', $object->id)->where('reason_id', $reasonId)->sum('amount');

                                    if ($total == 0) {
                                        continue;
                                    }
                                @endphp

                                <tr>
                                    <td class="ps-8 fs-8 fst-italic">{{ $reason }}</td>

                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($periods as $period)
                                        @php
                                            $plan = $plans->where('object_id', $object->id)->where('date', $period['start'])->where('reason_id', $reasonId)->first();
                                            $amount = 0;

                                            if ($plan) {
                                                $amount = $plan->amount;
                                            }


                                            $total += $amount;
                                        @endphp

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right fs-8 fst-italic pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                    </td>
                                </tr>
                            @endforeach

                        @endforeach

                        <tr class="divider-row">
                            <td colspan="{{ 2 + count($periods) }}"></td>
                        </tr>

                        @foreach($planPaymentTypes as $type)
                            <tr>
                                <td class="ps-2">{{ $type }}</td>

                                @php
                                    $total = 0;
                                @endphp
                                @foreach($periods as $index => $period)
                                    @php
                                        if ($index === 0) {
                                            $amount = $planPayments->where('name', $type)->where('due_date', '<=', $period['end'])->sum('amount');
                                        } else {
                                            $amount = $planPayments->where('name', $type)->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
                                        }
                                        $total += $amount;
                                    @endphp

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
                                @endforeach

                                <td class="text-right pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                </td>
                            </tr>
                        @endforeach

                        <tr class="divider-row">
                            <td colspan="{{ 2 + count($periods) }}"></td>
                        </tr>

                        <tr class="object-row">
                            <td class="ps-2 fw-bolder">Итого расходов по неделям:</td>

                            @php
                                $total = 0;
                            @endphp
                            @foreach($periods as $index => $period)
                                @php
                                    if ($index === 0) {
                                        $amount = $planPayments->where('due_date', '<=', $period['end'])->sum('amount');
                                    } else {
                                        $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
                                    }

                                    $total += $amount;
                                @endphp

                                <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
                            @endforeach

                            <td class="text-right pe-2 fw-bolder">
                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                            </td>
                        </tr>

                        <tr class="object-row">
                            <td class="ps-2 fw-bolder">Итого расходов по месяцам:</td>

                            @foreach($periods as $period)
                                @php
//                                    $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
                                @endphp

                                <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}</td>
                            @endforeach

                            <td class="text-right pe-2"></td>
                        </tr>

                        <tr class="object-row">
                            <td class="ps-2 fw-bolder">Сальдо (без учета целевых авансов) по неделям:</td>

                            @foreach($periods as $index => $period)
                                @php
                                    $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

                                    if ($index === 0) {
                                        $amount = $planPayments->where('due_date', '<=', $period['end'])->sum('amount');
                                    } else {
                                        $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
                                    }

                                    $diff = $otherAmount - $amount;
                                @endphp

                                <td class="cell-center fw-bolder {{ $diff < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($diff, 'RUB', 0, true) }}</td>
                            @endforeach

                            <td class="text-right pe-2"></td>
                        </tr>

                        <tr class="object-row">
                            <td class="ps-2 fw-bolder">Накопительное Сальдо (без учета целевых авансов) по неделям:</td>

                            @php
                                $prev = 0;
                            @endphp

                            @foreach($periods as $index => $period)
                                @php
                                    $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

                                    if ($index === 0) {
                                        $amount = $planPayments->where('due_date', '<=', $period['end'])->sum('amount');
                                    } else {
                                        $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
                                    }

                                    $diff = $otherAmount - $amount + $prev;
                                    $prev = $diff;
                                @endphp

                                <td class="cell-center fw-bolder {{ $diff < 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($diff, 'RUB', 0, true) }}</td>
                            @endforeach

                            <td class="text-right pe-2"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });
    </script>
@endpush

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
            background-color: #e7e7e7 !important;
            font-weight: bold !important;
        }

        .object-row {
            background-color: #f7f7f7 !important;
        }

        .divider-row td {
            height: 6px;
            padding: 0 !important;
        }
    </style>
@endpush
