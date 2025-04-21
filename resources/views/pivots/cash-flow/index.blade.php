@extends('layouts.app')

@section('title', 'Отчет CASH FLOW')
@section('toolbar-title', 'Отчет CASH FLOW')
@section('breadcrumbs', Breadcrumbs::render('pivots.cash_flow.index'))

@section('content')

    @include('pivots.cash-flow.modals.group-payments')
    @include('pivots.cash-flow.modals.notifications')
    @include('pivots.cash-flow.modals.filter')

    <div class="card mb-5 mb-xl-8 border-0">
        <div class="card-header border-0">
            <div class="card-title"></div>

            <div class="card-toolbar">
                <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterCashFlowModal">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                        </svg>
                    </span>
                    Фильтр
                </button>

                @if ($isNotificationsAvailable)
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                        Уведомления
                        @if($hasUnreadNotifications)
                            <i class="ms-3 fa fa-info-circle text-danger"></i>
                        @endif
                    </button>
                @endif

                @can('index cash-flow-plan-payments')
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#groupPaymentsModal">
                        Группировка планов расходов
                    </button>
                @endcan

                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('pivots.cash_flow.exports.store') . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
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
                <table
                        class="table table-bordered align-middle table-row-dashed fs-6 gy-3 table-cf"
                        data-update-payment-url="{{ route('pivots.cash_flow.plan_payments.entries.store') }}"
                        data-update-payment-table-url="{{ route('pivots.cash_flow.plan_payments.table.index') }}"
                        data-update-comment-url="{{ route('pivots.cash_flow.comments.update') }}"
                >
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                            <th class="min-w-400px ps-2 text-start">Остаток денежных средств на начало дня на счетах: <span class="fw-boldest">{{ \Carbon\Carbon::now()->format('d.m.Y') }}</span></th>
                            <th class="min-w-125px">{{ \App\Models\CurrencyExchangeRate::format(array_sum($accounts), 'RUB', 0, true) }}</th>
                            @foreach($periods as $period)
                                <th class="min-w-250px">{{ $period['format'] }}</th>
                            @endforeach

                            <th class="min-w-250px">ИТОГО</th>
                        </tr>
                    </thead>

                    <tbody class="text-gray-600 fw-bold fs-7">
                    @foreach($accounts as $accountName => $amount)
                        <tr class="text-start text-muted fs-8 gs-0">
                            <td class="min-w-400px ps-8 fw-bolder">{{ $accountName }}</td>
                            <td class="min-w-50px text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>

                            @foreach($periods as $period)
                                <td class="min-w-250px text-right fst-italic">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                            @endforeach

                            <td class="min-w-250px text-right pe-2 fst-italic">
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        </tr>
                    @endforeach

                    <tr class="text-start text-muted fw-bolder fs-7 gs-0 total-row">
                        <td class="min-w-400px ps-2">САЛЬДО ИТОГО, в том числе:</td>
                        <td class="min-w-50px text-center">Код объекта</td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $receive = $plans->where('date', $period['start'])->sum('amount');
                                $payments = $cfPayments['total']['all'][$period['start']];
                                $total += $receive + $payments;
                            @endphp
                            <td class="min-w-250px text-right">
                                {{ \App\Models\CurrencyExchangeRate::format(($receive + $payments), 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                    <tr class="text-start text-muted fw-bolder fs-7 gs-0 object-row">
                        <td class="min-w-400px ps-5">ПОСТУПЛЕНИЯ ИТОГО, в том числе:</td>
                        <td class="min-w-50px"></td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $amount = $plans->where('date', $period['start'])->sum('amount');
                                $total += $amount;
                            @endphp
                            <td class="min-w-250px text-right">
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                    <tr class="text-start text-muted fs-8 gs-0">
                        <td class="min-w-400px ps-8 fw-bolder">Целевые авансы</td>
                        <td class="min-w-50px"></td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $amount = $plans->where('date', $period['start'])->where('reason_id', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
                                $total += $amount;

                                $comment = \App\Models\CashFlow\Comment::where('period', $period['format'])
                                    ->where('type_id', \App\Models\CashFlow\Comment::TYPE_RECEIVE)
                                    ->where('target_info', ';Целевые авансы')
                                    ->first();
                            @endphp
                            <td
                                    class="min-w-250px text-right fst-italic cf-comment"
                                    data-comment="{{ $comment->text ?? '' }}"
                                    data-comment-id="{{ $comment->id ?? null }}"
                                    data-type-id="{{ \App\Models\CashFlow\Comment::TYPE_RECEIVE }}"
                                    data-period="{{ $period['format'] }}"
                                    data-reason="Целевые авансы"
                                    data-object="null"
                            >
                                <div class="comment-marker {{ $comment ? 'has-comment' : '' }}"></div>
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2 fst-italic">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                    <tr class="text-start text-muted fs-8 gs-0">
                        <td class="min-w-400px ps-8 fw-bolder">Прочие поступления</td>
                        <td class="min-w-50px"></td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $amount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
                                $total += $amount;

                                $comment = \App\Models\CashFlow\Comment::where('period', $period['format'])
                                    ->where('type_id', \App\Models\CashFlow\Comment::TYPE_RECEIVE)
                                    ->where('target_info', ';Прочие поступления')
                                    ->first();
                            @endphp
                            <td class="min-w-250px text-right fst-italic cf-comment"
                                data-comment="{{ $comment->text ?? '' }}"
                                data-comment-id="{{ $comment->id ?? null }}"
                                data-type-id="{{ \App\Models\CashFlow\Comment::TYPE_RECEIVE }}"
                                data-period="{{ $period['format'] }}"
                                data-reason="Прочие поступления"
                                data-object="null"
                            >
                                <div class="comment-marker {{ $comment ? 'has-comment' : '' }}"></div>
                                {{--                                    <button type="button" class="btn btn-secondary my-2 me-5" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="It's very engaging. Right?">--}}
                                {{--                                        Popover on top--}}
                                {{--                                    </button>--}}
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2 fst-italic">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                    <tr class="text-start text-muted fw-bolder fs-7 gs-0 object-row">
                        <td class="min-w-400px ps-5">РАСХОДЫ ИТОГО, в том числе:</td>
                        <td class="min-w-50px"></td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $amount = $cfPayments['total']['all'][$period['start']];
                                $total += $amount;
                            @endphp
                            <td class="min-w-250px text-right">
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                    <tr class="text-start text-muted fs-8 gs-0">
                        <td class="min-w-400px ps-8 fw-bolder">Работы</td>
                        <td class="min-w-50px"></td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $amount = $cfPayments['total']['contractors'][$period['start']];
                                $total += $amount;
                            @endphp
                            <td class="min-w-250px text-right fst-italic">
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2 fst-italic">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                    <tr class="text-start text-muted fs-8 gs-0">
                        <td class="min-w-400px ps-8 fw-bolder">Материалы</td>
                        <td class="min-w-50px"></td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $amount = $cfPayments['total']['providers_fix'][$period['start']] + $cfPayments['total']['providers_float'][$period['start']];
                                $total += $amount;
                            @endphp
                            <td class="min-w-250px text-right fst-italic">
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2 fst-italic">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                    <tr class="text-start text-muted fs-8 gs-0">
                        <td class="min-w-400px ps-8 fw-bolder">Накладные/Услуги</td>
                        <td class="min-w-50px"></td>

                        @php
                            $total = 0;
                        @endphp
                        @foreach($periods as $period)
                            @php
                                $amount = $cfPayments['total']['service'][$period['start']];
                                $total += $amount;
                            @endphp
                            <td class="min-w-250px text-right fst-italic">
                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                            </td>
                        @endforeach

                        <td class="min-w-250px text-right pe-2 fst-italic">
                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                        </td>
                    </tr>

                        @foreach($objects as $object)
                            @php
                                if (count($filteredObjects) > 0 && ! in_array($object->id, $filteredObjects)) {
                                    continue;
                                }

                                $totalReceive = 0;
                                $totalPayment = 0;
                                $totalSaldo = 0;
                                foreach($periods as $period) {
                                     $totalReceive += $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
                                     $totalPayment += $cfPayments['objects'][$object->id][$period['start']]['total'] ?? 0;

                                     $totalSaldo += ($totalReceive + $totalPayment);
                                }

                                if ($totalSaldo == 0) {
                                    continue;
                                }
                            @endphp
                            <tr class="total-row">
                                <td class="ps-2 fw-bolder">{{ $object->name }}</td>
                                <td>{{ $object->code }}</td>

                                @foreach($periods as $period)
                                    @php
                                        $receive = $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
                                        $payment = $cfPayments['objects'][$object->id][$period['start']]['total'] ?? 0;
                                    @endphp
                                    <td class="text-right fw-bolder">
                                        {{ \App\Models\CurrencyExchangeRate::format(($receive + $payment), 'RUB', 0, true) }}
                                    </td>
                                @endforeach

                                <td class="text-right fw-bolder pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($totalSaldo, 'RUB', 0, true) }}
                                </td>
                            </tr>

                            <tr class="text-start text-muted fw-bolder fs-7 gs-0 object-row">
                                <td class="min-w-400px ps-5">ПОСТУПЛЕНИЯ ИТОГО, в том числе:</td>
                                <td class="min-w-50px"></td>

                                @php
                                    $total = 0;
                                @endphp
                                @foreach($periods as $period)
                                    @php
                                        $amount = $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
                                        $total += $amount;
                                    @endphp
                                    <td class="min-w-250px text-right">
                                        {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                    </td>
                                @endforeach

                                <td class="min-w-250px text-right pe-2">
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
                                    <td></td>

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

                                            $comment = \App\Models\CashFlow\Comment::where('period', $period['format'])
                                                ->where('type_id', \App\Models\CashFlow\Comment::TYPE_RECEIVE)
                                                ->where('target_info', $object->code . ';' . $reason)
                                                ->first();
                                        @endphp

                                        <td class="text-right fs-8 fst-italic cf-comment"
                                            data-comment="{{ $comment->text ?? '' }}"
                                            data-comment-id="{{ $comment->id ?? null }}"
                                            data-type-id="{{ \App\Models\CashFlow\Comment::TYPE_RECEIVE }}"
                                            data-period="{{ $period['format'] }}"
                                            data-reason="{{ $reason }}"
                                            data-object="{{ $object->code }}"
                                        >
                                            <div class="comment-marker {{ $comment ? 'has-comment' : '' }}"></div>
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                        </td>
                                    @endforeach

                                    <td class="text-right fs-8 fst-italic pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                    </td>
                                </tr>
                            @endforeach

                            @if (isset($cfPayments['objects'][$object->id]))
                                <tr class="text-start text-muted fw-bolder fs-7 gs-0 object-row">
                                    <td class="min-w-400px ps-5">РАСХОДЫ ИТОГО, в том числе:</td>
                                    <td class="min-w-50px"></td>

                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($periods as $period)
                                        @php
                                            $amount = $cfPayments['objects'][$object->id][$period['start']]['total'] ?? 0;
                                            $total += $amount;
                                        @endphp
                                        <td class="min-w-250px text-right">
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                        </td>
                                    @endforeach

                                    <td class="min-w-250px text-right pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                    </td>
                                </tr>

                                @php
                                    $totalContractors = 0;
                                    $totalProviders = 0;
                                    $totalService = 0;

                                    foreach ($periods as $period) {
                                        $totalContractors += $cfPayments['objects'][$object->id][$period['start']]['contractors'] ?? 0;
                                        $totalProviders += ($cfPayments['objects'][$object->id][$period['start']]['providers_fix'] ?? 0) + ($cfPayments['objects'][$object->id][$period['start']]['providers_float'] ?? 0);
                                        $totalService += $cfPayments['objects'][$object->id][$period['start']]['service'] ?? 0;
                                    }
                                @endphp

                                @if (is_valid_amount_in_range($totalContractors))
                                    <tr>
                                        <td class="ps-8 fs-8 fst-italic">
                                            <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="contractors-cf">+</span>

                                            Работы
                                        </td>
                                        <td></td>

                                        @php
                                            $total = 0;
                                        @endphp
                                        @foreach($periods as $period)
                                            @php
                                                $amount = $cfPayments['objects'][$object->id][$period['start']]['contractors'] ?? 0;
                                                $total += $amount;
                                            @endphp

                                            <td class="text-right fs-8 fst-italic">
                                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                            </td>
                                        @endforeach

                                        <td class="text-right fs-8 fst-italic pe-2">
                                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                        </td>
                                    </tr>

                                    @foreach($cfPayments['objects_details'][$object->id]['contractors'] as $contractorName => $info)
                                        <tr class="collapse-row" data-trigger="contractors-cf" style="display: none;">
                                            <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                            <td></td>

                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach($periods as $period)
                                                @php
                                                    $amount = $info[$period['start']] ?? 0;
                                                    $total += $amount;
                                                @endphp

                                                <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                            @endforeach

                                            <td class="text-right fs-8 fst-italic pe-2">
                                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                @if (is_valid_amount_in_range($totalProviders))
                                    <tr>
                                        <td class="ps-8 fs-8 fst-italic">
                                            @if ($object->code !== '363')
                                                <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="providers-cf">+</span>
                                            @endif

                                            Материалы
                                        </td>
                                        <td></td>

                                        @php
                                            $total = 0;
                                        @endphp
                                        @foreach($periods as $period)
                                            @php
                                                $amount = ($cfPayments['objects'][$object->id][$period['start']]['providers_fix'] ?? 0) + ($cfPayments['objects'][$object->id][$period['start']]['providers_float'] ?? 0);
                                                $total += $amount;
                                            @endphp

                                            <td class="text-right fs-8 fst-italic">
                                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                            </td>
                                        @endforeach

                                        <td class="text-right fs-8 fst-italic pe-2">
                                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                        </td>
                                    </tr>

                                    @if ($object->code !== '363')
                                        @foreach($cfPayments['objects_details'][$object->id]['providers'] as $contractorName => $info)
                                            <tr class="collapse-row" data-trigger="providers-cf" style="display: none;">
                                                <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                                <td></td>

                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach($periods as $period)
                                                    @php
                                                        $amount = $info[$period['start']] ?? 0;
                                                        $total += $amount;
                                                    @endphp

                                                    <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                                @endforeach

                                                <td class="text-right fs-8 fst-italic pe-2">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    @if ($object->code === '363')

                                        <tr>
                                            <td class="ps-10 fs-8 fst-italic">
                                                <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="providers-fix-cf">+</span>

                                                - фиксированная часть
                                            </td>
                                            <td></td>

                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach($periods as $period)
                                                @php
                                                    $amount = ($cfPayments['objects'][$object->id][$period['start']]['providers_fix'] ?? 0);
                                                    $total += $amount;
                                                @endphp

                                                <td class="text-right fs-8 fst-italic">
                                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                                </td>
                                            @endforeach

                                            <td class="text-right fs-8 fst-italic pe-2">
                                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                            </td>
                                        </tr>

                                        @foreach($cfPayments['objects_details'][$object->id]['providers_fix'] as $contractorName => $info)
                                            <tr class="collapse-row" data-trigger="providers-fix-cf" style="display: none;">
                                                <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                                <td></td>

                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach($periods as $period)
                                                    @php
                                                        $amount = $info[$period['start']] ?? 0;
                                                        $total += $amount;
                                                    @endphp

                                                    <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                                @endforeach

                                                <td class="text-right fs-8 fst-italic pe-2">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td class="ps-10 fs-8 fst-italic">
                                                <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="providers-float-cf">+</span>

                                                - изменяемая часть
                                            </td>
                                            <td></td>

                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach($periods as $period)
                                                @php
                                                    $amount = ($cfPayments['objects'][$object->id][$period['start']]['providers_float'] ?? 0);
                                                    $total += $amount;
                                                @endphp

                                                <td class="text-right fs-8 fst-italic">
                                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                                </td>
                                            @endforeach

                                            <td class="text-right fs-8 fst-italic pe-2">
                                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                            </td>
                                        </tr>

                                        @foreach($cfPayments['objects_details'][$object->id]['providers_float'] as $contractorName => $info)
                                            <tr class="collapse-row" data-trigger="providers-float-cf" style="display: none;">
                                                <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                                <td></td>

                                                @php
                                                    $total = 0;
                                                @endphp
                                                @foreach($periods as $period)
                                                    @php
                                                        $amount = $info[$period['start']] ?? 0;
                                                        $total += $amount;
                                                    @endphp

                                                    <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                                @endforeach

                                                <td class="text-right fs-8 fst-italic pe-2">
                                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                    @endif
                                @endif

                                @if (is_valid_amount_in_range($totalService))
                                    <tr>
                                        <td class="ps-8 fs-8 fst-italic">
                                            <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="service-cf">+</span>

                                            Накладные/Услуги
                                        </td>
                                        <td></td>

                                        @php
                                            $total = 0;
                                        @endphp
                                        @foreach($periods as $period)
                                            @php
                                                $amount = $cfPayments['objects'][$object->id][$period['start']]['service'] ?? 0;
                                                $total += $amount;
                                            @endphp

                                            <td class="text-right fs-8 fst-italic">
                                                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                            </td>
                                        @endforeach

                                        <td class="text-right fs-8 fst-italic pe-2">
                                            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                        </td>
                                    </tr>

                                    @foreach($cfPayments['objects_details'][$object->id]['service'] as $contractorName => $info)
                                        <tr class="collapse-row" data-trigger="service-cf" style="display: none;">
                                            <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                            <td></td>

                                            @php
                                                $total = 0;
                                            @endphp
                                            @foreach($periods as $period)
                                                @php
                                                    $amount = $info[$period['start']] ?? 0;
                                                    $total += $amount;
                                                @endphp

                                                <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                            @endforeach

                                            <td class="text-right fs-8 fst-italic pe-2">
                                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endif

                        @endforeach

                        <tr class="text-start text-muted fw-bolder fs-7 gs-0 total-row">
                            <td class="min-w-400px ps-2">Общие расходы</td>
                            <td class="min-w-50px text-center"></td>

                            @php
                                $total = 0;
                            @endphp
                            @foreach($periods as $index => $period)
                                @php
                                    if ($index === 0) {
                                        $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments) - $cfPayments['total']['all'][$period['start']];
                                    } else {
                                        $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') - $cfPayments['total']['all'][$period['start']];
                                    }
                                @endphp
                                <td class="min-w-250px text-right">
                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                </td>
                            @endforeach

                            <td class="min-w-250px text-right pe-2">
                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                            </td>
                        </tr>

                        @php
                            $officeObjectId = \App\Models\Object\BObject::where('code', '27.1')->first()->id;
                        @endphp

                        <tr>
                            <td class="ps-2">
                                <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="office-object">+</span>

                                Расходы офиса
                            </td>
                            <td>27.1</td>

                            @php
                                $totalOfficeObject = 0;
                            @endphp
                            @foreach($periods as $period)
                                @php
                                    $amount = $cfPayments['objects'][$officeObjectId][$period['start']]['total'] ?? 0;
                                    $totalOfficeObject += $amount;
                                @endphp
                                <td class="text-right fw-bolder">
                                    {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                </td>
                            @endforeach

                            <td class="text-right fw-bolder pe-2">
                                {{ \App\Models\CurrencyExchangeRate::format($totalOfficeObject, 'RUB', 0, true) }}
                            </td>
                        </tr>

                        @php
                            $totalContractors = 0;
                            $totalProviders = 0;
                            $totalService = 0;

                            foreach ($periods as $period) {
                                $totalContractors += $cfPayments['objects'][$officeObjectId][$period['start']]['contractors'] ?? 0;
                                $totalProviders += ($cfPayments['objects'][$officeObjectId][$period['start']]['providers_fix'] ?? 0) + ($cfPayments['objects'][$officeObjectId][$period['start']]['providers_float'] ?? 0);
                                $totalService += $cfPayments['objects'][$officeObjectId][$period['start']]['service'] ?? 0;
                            }
                        @endphp

                        @if (is_valid_amount_in_range($totalContractors))
                            <tr class="collapse-row object-row fw-bolder" data-trigger="office-object" style="display: none;">
                                <td class="ps-8 fs-8 fst-italic">
                                    Работы
                                </td>
                                <td></td>

                                @php
                                    $total = 0;
                                @endphp
                                @foreach($periods as $period)
                                    @php
                                        $amount = $cfPayments['objects'][$officeObjectId][$period['start']]['contractors'] ?? 0;
                                        $total += $amount;
                                    @endphp

                                    <td class="text-right fs-8 fst-italic">
                                        {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                    </td>
                                @endforeach

                                <td class="text-right fs-8 fst-italic pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                </td>
                            </tr>

                            @foreach($cfPayments['objects_details'][$officeObjectId]['contractors'] as $contractorName => $info)
                                <tr class="collapse-row" data-trigger="office-object" style="display: none;">
                                    <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                    <td></td>

                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($periods as $period)
                                        @php
                                            $amount = $info[$period['start']] ?? 0;
                                            $total += $amount;
                                        @endphp

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right fs-8 fst-italic pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if (is_valid_amount_in_range($totalProviders))
                            <tr class="collapse-row object-row fw-bolder" data-trigger="office-object" style="display: none;">
                                <td class="ps-8 fs-8 fst-italic">
                                    Материалы
                                </td>
                                <td></td>

                                @php
                                    $total = 0;
                                @endphp
                                @foreach($periods as $period)
                                    @php
                                        $amount = ($cfPayments['objects'][$officeObjectId][$period['start']]['providers_fix'] ?? 0) + ($cfPayments['objects'][$officeObjectId][$period['start']]['providers_float'] ?? 0);
                                        $total += $amount;
                                    @endphp

                                    <td class="text-right fs-8 fst-italic">
                                        {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                    </td>
                                @endforeach

                                <td class="text-right fs-8 fst-italic pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                </td>
                            </tr>

                            @foreach($cfPayments['objects_details'][$officeObjectId]['providers'] as $contractorName => $info)
                                <tr class="collapse-row" data-trigger="office-object" style="display: none;">
                                    <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                    <td></td>

                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($periods as $period)
                                        @php
                                            $amount = $info[$period['start']] ?? 0;
                                            $total += $amount;
                                        @endphp

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right fs-8 fst-italic pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if (is_valid_amount_in_range($totalService))
                            <tr class="collapse-row object-row fw-bolder" data-trigger="office-object" style="display: none;">
                                <td class="ps-8 fs-8 fst-italic">
                                    Накладные/Услуги
                                </td>
                                <td></td>

                                @php
                                    $total = 0;
                                @endphp
                                @foreach($periods as $period)
                                    @php
                                        $amount = $cfPayments['objects'][$officeObjectId][$period['start']]['service'] ?? 0;
                                        $total += $amount;
                                    @endphp

                                    <td class="text-right fs-8 fst-italic">
                                        {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                    </td>
                                @endforeach

                                <td class="text-right fs-8 fst-italic pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                </td>
                            </tr>

                            @foreach($cfPayments['objects_details'][$officeObjectId]['service'] as $contractorName => $info)
                                <tr class="collapse-row" data-trigger="office-object" style="display: none;">
                                    <td class="ps-14 fs-8 fst-italic">{{ $contractorName }}</td>
                                    <td></td>

                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($periods as $period)
                                        @php
                                            $amount = $info[$period['start']] ?? 0;
                                            $total += $amount;
                                        @endphp

                                        <td class="text-right fs-8 fst-italic">{{ \App\Models\CurrencyExchangeRate::format($info[$period['start']] ?? 0, 'RUB', 0, true) }}</td>
                                    @endforeach

                                    <td class="text-right fs-8 fst-italic pe-2">
                                        {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @php
                            $planGroupedPaymentAmount = [];
                            foreach ($planPaymentGroups as $group) {
                                if ($group->payments->count() === 0) {
                                    continue;
                                }
                                $planGroupedPaymentAmount[$group->name] = [];

                                foreach ($group->payments as $payment) {
                                    foreach($periods as $index => $period) {
                                        if ($index === 0) {
                                            $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                                        } else {
                                            $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                                        }

                                        if (! isset($planGroupedPaymentAmount[$group->name][$period['id']])) {
                                            $planGroupedPaymentAmount[$group->name][$period['id']] = 0;
                                        }
                                        $planGroupedPaymentAmount[$group->name][$period['id']] += $amount;
                                    }
                                }
                            }
                        @endphp
                        @foreach($planPaymentGroups as $group)
                            @continue($group->payments->count() === 0)
                            <tr class="plan-payment">
                                <td class="ps-2">
                                    <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="{{ $group->name }}">+</span>
                                    {{ $group->name }}
                                </td>
                                <td>{{ $group->object->code ?? '' }}</td>

                                @php
                                    $groupTotal = 0;
                                @endphp
                                @foreach($periods as $index => $period)
                                    @php
                                        $amount = $planGroupedPaymentAmount[$group->name][$period['id']];
                                        $groupTotal += $amount;
                                    @endphp

                                    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
                                @endforeach

                                <td class="text-right pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($groupTotal, 'RUB', 0, true) }}
                                </td>
                            </tr>

                            @foreach($group->payments as $payment)
                                @include('pivots.cash-flow.partial.grouped_plan_payment_row', ['payment' => $payment, 'gr' => $group->name])
                            @endforeach
                        @endforeach

                        @foreach($CFPlanPayments as $payment)
                            @continue(!is_null($payment->group_id))

                            @include('pivots.cash-flow.partial.plan_payment_row', $payment)
                        @endforeach

                        @foreach($otherPlanPayments as $paymentName => $paymentAmount)
                            <tr class="plan-payment {{ !is_valid_amount_in_range($paymentAmount) ? 'd-none' : '' }}">
                                <td class="ps-2">
                                    {{ $paymentName }}
                                </td>
                                <td></td>

                                @php
                                    $total = 0;
                                @endphp
                                @foreach($periods as $index => $period)
                                    @php
                                        if ($index === 0) {
                                            $amount = $paymentAmount;
                                        } else {
                                            $amount = 0;
                                        }
                                        $total += $amount;

                                        $comment = \App\Models\CashFlow\Comment::where('period', $period['format'])
                                            ->where('type_id', \App\Models\CashFlow\Comment::TYPE_PAYMENT)
                                            ->where('target_info', ';' . $paymentName)
                                            ->first();
                                    @endphp

                                    <td class="text-right cf-comment"
                                        data-comment="{{ $comment->text ?? '' }}"
                                        data-comment-id="{{ $comment->id ?? null }}"
                                        data-type-id="{{ \App\Models\CashFlow\Comment::TYPE_PAYMENT }}"
                                        data-period="{{ $period['format'] }}"
                                        data-reason="{{ $paymentName }}"
                                        data-object="null"
                                    >
                                        <div class="comment-marker {{ $comment ? 'has-comment' : '' }}"></div>
                                        {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
                                    </td>
                                @endforeach

                                <td class="text-right pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                                </td>
                            </tr>
                        @endforeach

                        @can('index cash-flow-plan-payments')
                            <tr class="plan-payment">
                                <td class="ps-2">
                                    <input
                                            type="text"
                                            placeholder="Добавить запись"
                                            value=""
                                            class="form-control form-control-sm form-control-solid add-plan-payment-input"
                                            autocomplete="off"
                                            data-url="{{ route('pivots.cash_flow.plan_payments.store') }}"
                                    />
                                </td>
                                <td></td>

                                @foreach($periods as $index => $period)
                                    <td class="text-right"></td>
                                @endforeach

                                <td class="text-right pe-2">
                                    {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
                                </td>
                            </tr>
                        @endcan

                        <tr class="divider-row plan-payment">
                            <td colspan="{{ 3 + count($periods) }}"></td>
                        </tr>

                        <tr class="object-row plan-payment">
                            <td class="ps-2 fw-bolder">Итого расходов по неделям:</td>
                            <td></td>

                            @php
                                $total = 0;
                            @endphp
                            @foreach($periods as $index => $period)
                                @php
                                    if ($index === 0) {
                                        $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments) - $cfPayments['total']['all'][$period['start']];
                                    } else {
                                        $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') - $cfPayments['total']['all'][$period['start']];
                                    }

                                    $total += $amount;
                                @endphp

                                <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
                            @endforeach

                            <td class="text-right pe-2 fw-bolder">
                                {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
                            </td>
                        </tr>

{{--                        <tr class="object-row plan-payment">--}}
{{--                            <td class="ps-2 fw-bolder">Итого расходов по месяцам:</td>--}}
{{--                            <td></td>--}}

{{--                            @foreach($periods as $period)--}}
{{--                                @php--}}
{{--                                    //                                    $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');--}}
{{--                                @endphp--}}

{{--                                <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}</td>--}}
{{--                            @endforeach--}}

{{--                            <td class="text-right pe-2"></td>--}}
{{--                        </tr>--}}

                        <tr class="object-row plan-payment">
                            <td class="ps-2 fw-bolder">Сальдо (без учета целевых авансов) по неделям:</td>
                            <td></td>

                            @foreach($periods as $index => $period)
                                @php
                                    $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

                                    if ($index === 0) {
                                        $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments) - $cfPayments['total']['all'][$period['start']] + array_sum($accounts);
                                    } else {
                                        $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') - $cfPayments['total']['all'][$period['start']];
                                    }

                                    $diff = $otherAmount - $amount;
                                @endphp

                                <td class="cell-center fw-bolder {{ $diff < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($diff, 'RUB', 0, true) }}</td>
                            @endforeach

                            <td class="text-right pe-2"></td>
                        </tr>

                        <tr class="object-row plan-payment">
                            <td class="ps-2 fw-bolder">Накопительное Сальдо (без учета целевых авансов) по неделям:</td>
                            <td></td>

                            @php
                                $prev = 0;
                            @endphp

                            @foreach($periods as $index => $period)
                                @php
                                    $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

                                    if ($index === 0) {
                                        $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments) - $cfPayments['total']['all'][$period['start']] + array_sum($accounts);
                                    } else {
                                        $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') - $cfPayments['total']['all'][$period['start']];
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
            $('.collapse-row').hide();

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
            });

            $(document).on('click', '.plan-payment-name', function() {
                $(this).parent().find('input').val($(this).text()).show().focus();
                $(this).hide();
            });

            $(document).on('blur', '.update-plan-payment-input', function() {
                const span = $(this).parent().find('span');
                const payment_id = $(this).data('payment-id');
                const group = $(this).closest('tr').data('group');

                if (span.text() !== $(this).val()) {

                    if ($(this).val().length > 0) {

                        const url = $(this).data('url');
                        const name = $(this).val();

                        mainApp.sendAJAX(
                            url,
                            'POST',
                            {payment_id, name, group},
                            (data) => {
                                $(this).closest('tr').replaceWith(data.view);
                                mainApp.init();
                                KTApp.initSelect2();
                                mainApp.initFreezeTable(1);
                            }
                        );
                    } else {
                        if (confirm('Вы уверены, что хотите удалить запись расхода?')) {
                            const url = $(this).data('destroy-url');
                            mainApp.sendAJAX(
                                url,
                                'POST',
                                {payment_id},
                                () => {
                                    $(this).closest('tr').remove();
                                    mainApp.initFreezeTable(1);
                                }
                            );
                        } else {
                            $(this).val('').hide();
                            span.show();
                        }
                    }
                } else {
                    $(this).val('').hide();
                    span.show();
                }
            });

            $(document).on('change', '.update-plan-payment-select', function() {
                const payment_id = $(this).data('payment-id');
                const url = $(this).data('url');
                const object_id = $(this).val();
                const group = $(this).closest('tr').data('group');

                mainApp.sendAJAX(
                    url,
                    'POST',
                    {payment_id, object_id, group},
                    (data) => {
                        $(this).closest('tr').replaceWith(data.view);
                        mainApp.init();
                        KTApp.initSelect2();
                        mainApp.initFreezeTable(1);
                    }
                );
            });

            $(document).on('blur', '.add-plan-payment-input', function() {
                if ($(this).val().length > 0) {
                    const url = $(this).data('url');
                    const name = $(this).val();
                    const group = $(this).closest('tr').data('group');

                    mainApp.sendAJAX(
                        url,
                        'POST',
                        {name, group},
                        (data) => {
                            $(this).val('');
                            $(this).closest('tr').before(data.view);
                            mainApp.init();
                            KTApp.initSelect2();
                            mainApp.initFreezeTable(1);
                        }
                    );
                }
            });
        });

        $(document).on('focus', '.db-field', function() {
            $(this).data('initial-amount', $(this).val());
        });

        $(document).on('blur', '.db-field', function() {
            const $that = $(this);
            const payment_id = $that.data('payment-id');
            const date = $that.data('date');
            const amount = $that.val();
            const url = $('.table-cf').data('update-payment-url');
            const group = $(this).closest('tr').data('group');

            if ($that.data('initial-amount') !== amount) {
                mainApp.sendAJAX(
                    url,
                    'POST',
                    {
                        payment_id,
                        date,
                        amount,
                        group
                    },
                    (data) => {
                        $(this).closest('tr').replaceWith(data.view);
                        mainApp.init();
                        KTApp.initSelect2();
                        mainApp.initFreezeTable(1);
                    }
                );
            }
        });

        function updatePlanPaymentTable() {
            const url = $('.table-cf').data('update-payment-table-url');

            mainApp.sendAJAX(
                url,
                'GET',
                {},
                (data) => {
                    $('.table-cf tr.plan-payment').remove();
                    $('.table-cf .divider-before-payments').after(data.view);
                }
            );
        }

        $('.cf-comment').on('dblclick', function() {
            $('.cf-comment-container').hide();
            const $that = $(this);

            const outerWidth = $that.outerWidth();
            $that.addClass('position-relative');
            $that.append(
                `
                    <div class="cf-comment-container" style="left: ${(outerWidth - 200) / 2}px">
                        <textarea name="cf_comment" rows="3" placeholder="Введите комментарий"></textarea>
                    </div>
                `
            );

            const typeId = $that.data('type-id');
            const commentId = $that.data('comment-id');
            const period = $that.data('period');
            const object = $that.data('object');
            const reason = $that.data('reason');
            const url = $('.table-cf').data('update-comment-url');

            const initialComment = ''+$that.data('comment') ?? '';

            $that.find('textarea').val(initialComment).focus();

            $that.find('textarea').on('blur', function() {
                const comment = ''+$(this).val();

                if (comment === initialComment) {
                    $(this).parent().remove();
                    return true;
                }

                mainApp.sendAJAX(
                    url,
                    'POST',
                    {typeId, period, object, reason, comment, commentId},
                    (data) => {
                        $(this).parent().parent().data('comment', comment);
                        $(this).parent().parent().data('comment-id', data.comment_id);
                        $(this).parent().remove();

                        if (! $that.find('.comment-marker').hasClass('has-comment')) {
                            $that.find('.comment-marker').addClass('has-comment');
                        }

                        if (comment.length === 0) {
                            $that.find('.comment-marker').removeClass('has-comment');
                        }
                    }
                );
            });
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

        .add-plan-payment:hover {
            font-weight: bold;
        }
    </style>
@endpush
