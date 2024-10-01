@php
    $periodsAmount = [];
    $planGroupedPaymentAmount = [];
    foreach ($planPaymentGroups as $group) {
        $planGroupedPaymentAmount[$group->name] = [];

        foreach ($group->payments as $payment) {
            foreach($periods as $index => $period) {
                if ($index === 0) {
                    $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                } else {
                    $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                }

                if (! isset($planGroupedPaymentAmount[$group->name][$period['format']])) {
                    $planGroupedPaymentAmount[$group->name][$period['format']] = 0;
                }
                $planGroupedPaymentAmount[$group->name][$period['format']] += $amount;
            }
        }
    }
@endphp
@foreach($planPaymentGroups as $group)
    <tr class="plan-payment">
        <td class="ps-2">
            <span class="pe-2 fs-2 fw-bold collapse-trigger cursor-pointer cell-center" data-trigger="{{ $group->name }}">+</span>
            {{ $group->name }}
        </td>

        @php
            $groupTotal = 0;
        @endphp
        @foreach($periods as $index => $period)
            @php
                $amount = $planGroupedPaymentAmount[$group->name][$period['format']];
                $groupTotal += $amount;
            @endphp

            <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
        @endforeach

        <td class="text-right pe-2">
            {{ \App\Models\CurrencyExchangeRate::format($groupTotal, 'RUB', 0, true) }}
        </td>
    </tr>

    @foreach($group->payments as $payment)
        <tr class="collapse-row plan-payment" data-trigger="{{ $group->name }}" style="display: none;">
            <td class="ps-8">{{ $payment->name }}</td>

            @php
                $total = 0;
            @endphp
            @foreach($periods as $index => $period)
                @php
                    if ($index === 0) {
                        $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                    } else {
                        $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
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
@endforeach

@foreach($CFPlanPayments as $payment)
    @continue(!is_null($payment->group_id))

    <tr class="plan-payment">
        <td class="ps-2">{{ $payment->name }}</td>

        @php
            $total = 0;
        @endphp
        @foreach($periods as $index => $period)
            @php
                if ($index === 0) {
                    $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                } else {
                    $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                }
                $total += $amount;
            @endphp

            <td class="text-right">
                <input
                        type="text"
                        value="{{ $amount }}"
                        class="amount-mask form-control form-control-sm form-control-solid db-field"
                        autocomplete="off"
                        data-payment-id="{{ $payment->id }}"
                        data-date="{{ $period['start'] }}"
                />
        @endforeach

        <td class="text-right pe-2">
            {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
        </td>
    </tr>
@endforeach

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

    @foreach($periods as $index => $period)
        <td class="text-right"></td>
    @endforeach

    <td class="text-right pe-2">
        {{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}
    </td>
</tr>

<tr class="divider-row plan-payment">
    <td colspan="{{ 2 + count($periods) }}"></td>
</tr>

<tr class="object-row plan-payment">
    <td class="ps-2 fw-bolder">Итого расходов по неделям:</td>

    @php
        $total = 0;
    @endphp
    @foreach($periods as $index => $period)
        @php
            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount');
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }

            $total += $amount;
        @endphp

        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>
    @endforeach

    <td class="text-right pe-2 fw-bolder">
        {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
    </td>
</tr>

<tr class="object-row plan-payment">
    <td class="ps-2 fw-bolder">Итого расходов по месяцам:</td>

    @foreach($periods as $period)
        @php
            //                                    $amount = $planPayments->whereBetween('due_date', [$period['start'], $period['end']])->sum('amount');
        @endphp

        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format(0, 'RUB', 0, true) }}</td>
    @endforeach

    <td class="text-right pe-2"></td>
</tr>

<tr class="object-row plan-payment">
    <td class="ps-2 fw-bolder">Сальдо (без учета целевых авансов) по неделям:</td>

    @foreach($periods as $index => $period)
        @php
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount');
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }

            $diff = $otherAmount - $amount;
        @endphp

        <td class="cell-center fw-bolder {{ $diff < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($diff, 'RUB', 0, true) }}</td>
    @endforeach

    <td class="text-right pe-2"></td>
</tr>

<tr class="object-row plan-payment">
    <td class="ps-2 fw-bolder">Накопительное Сальдо (без учета целевых авансов) по неделям:</td>

    @php
        $prev = 0;
    @endphp

    @foreach($periods as $index => $period)
        @php
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount');
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }

            $diff = $otherAmount - $amount + $prev;
            $prev = $diff;
        @endphp

        <td class="cell-center fw-bolder {{ $diff < 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($diff, 'RUB', 0, true) }}</td>
    @endforeach

    <td class="text-right pe-2"></td>
</tr>