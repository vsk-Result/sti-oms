<tr class="plan-payment">
    <td class="ps-2">
        <span class="{{ auth()->user()->can('index cash-flow-plan-payments') ? 'cursor-pointer plan-payment-name' : '' }}">{{ $payment->name }}</span>
        @can('index cash-flow-plan-payments')
            <input
                    type="text"
                    value=""
                    class="form-control form-control-sm form-control-solid update-plan-payment-input"
                    autocomplete="off"
                    data-url="{{ route('pivots.cash_flow.plan_payments.update') }}"
                    data-destroy-url="{{ route('pivots.cash_flow.plan_payments.destroy') }}"
                    data-payment-id="{{ $payment->id }}"
                    style="display: none;"
            />
        @endcan
    </td>
    <td>
        @if (auth()->user()->can('index cash-flow-plan-payments'))
            <select
                    name="object_id"
                    data-control="select2"
                    class="form-select form-select-solid  update-plan-payment-select"
                    data-url="{{ route('pivots.cash_flow.plan_payments.update') }}"
                    data-payment-id="{{ $payment->id }}"
            >
                <option value="null" {{ is_null($payment->object_id) ? 'selected' : '' }}>-</option>
                @foreach($objectList as $o)
                    <option value="{{ $o->id }}" {{ $o->id == $payment->object_id ? 'selected' : '' }}>{{ $o->code }}</option>
                @endforeach
            </select>
        @else
            {{ $payment->object->code ?? '' }}
        @endif
    </td>

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
            @if (auth()->user()->can('index cash-flow-plan-payments'))
                <input
                        type="text"
                        value="{{ $amount }}"
                        class="amount-mask form-control form-control-sm form-control-solid db-field"
                        autocomplete="off"
                        data-payment-id="{{ $payment->id }}"
                        data-date="{{ $period['start'] }}"
                />
            @else
                {{ \App\Models\CurrencyExchangeRate::format($amount, 'RUB', 0, true) }}
            @endif
    @endforeach

    <td class="text-right pe-2">
        {{ \App\Models\CurrencyExchangeRate::format($total, 'RUB', 0, true) }}
    </td>
</tr>