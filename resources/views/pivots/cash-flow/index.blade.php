@extends('layouts.app')

@section('toolbar-title', 'Отчет CASH FLOW')
@section('breadcrumbs', Breadcrumbs::render('pivots.cash_flow.index'))

@section('content')
    <div class="card mb-5 mb-xl-8 border-0">
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
