<tr class="object-row plan-payment">
    <td class="ps-2 fw-bolder">Итого расходов по неделям:</td>
    <td></td>

    @php
        $amount = -abs($CFPlanPaymentEntries->where('date', '<', $periods[0]['start'])->sum('amount')) + -abs($cfPayments['total']['all']['no_paid']);
        $total = $amount;
    @endphp

    <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}</td>

    @foreach($periods as $index => $period)
        @php
            if ($index === 0) {
                $amount = -abs($CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount')) + -abs(array_sum($otherPlanPayments)) + -abs($cfPayments['total']['all'][$period['start']]);
            } else {
                $amount = -abs($CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount')) + -abs($cfPayments['total']['all'][$period['start']]);
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
    <td class="ps-2 fw-bolder">Сальдо (без учета целевых авансов) по неделям:</td>
    <td></td>

    @php
        $otherAmount = $plans->where('date', '<', $periods[0]['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
        $amount = $CFPlanPaymentEntries->where('date', '<', $periods[0]['start'])->sum('amount') - $cfPayments['total']['all']['no_paid'];

        $diff = $otherAmount - $amount;
    @endphp

    <td class="cell-center fw-bolder {{ $diff < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($diff, 'RUB', 0, true) }}</td>

    @foreach($periods as $index => $period)
        @php
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') + array_sum($otherPlanPayments) - $cfPayments['total']['all'][$period['start']] + array_sum($accounts);
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
        $otherAmount = $plans->where('date', '<', $periods[0]['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
        $amount = $CFPlanPaymentEntries->where('date', '<', $periods[0]['start'])->sum('amount') - $cfPayments['total']['all']['no_paid'];

        $diff = $otherAmount - $amount;
        $prev = $diff;
    @endphp

    <td class="cell-center fw-bolder {{ $diff < 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($diff, 'RUB', 0, true) }}</td>

    @foreach($periods as $index => $period)
        @php
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', \App\Models\Object\ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount') + array_sum($otherPlanPayments) - $cfPayments['total']['all'][$period['start']] + array_sum($accounts);
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