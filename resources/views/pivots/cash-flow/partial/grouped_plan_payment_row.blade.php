@php
    $total = $payment->entries->where('date', '<=', last($periods)['end'])->sum('amount');
@endphp

<tr class="collapse-row plan-payment {{ !is_valid_amount_in_range($total) && (auth()->id() !== 30 && auth()->id() !== 12 && auth()->id() !== 31) ? 'd-none' : '' }}" data-trigger="{{ $gr }}" data-group="{{ $gr }}">
    <td class="ps-8">
        <span class="{{ auth()->user()->can('index cash-flow-plan-payments') && !$payment->from_tax_plan ? 'cursor-pointer plan-payment-name' : '' }}">{{ $payment->name }}</span>
        @if (!$payment->from_tax_plan)
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
        @endif
    </td>
    <td class="text-center">
        @if (auth()->user()->can('index cash-flow-plan-payments') && !$payment->from_tax_plan)
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

    @foreach($periods as $index => $period)
        @php
            if ($index === 0) {
                $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
            } else {
                $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }
            $comment = \App\Models\CashFlow\Comment::where('period', $period['format'])
                    ->where('type_id', \App\Models\CashFlow\Comment::TYPE_PAYMENT)
                    ->where('target_info', ($payment->object->code ?? '') . ';' . $gr . ';' . $payment->name)
                    ->first();
        @endphp

        <td class="text-right cf-comment"
            data-comment="{{ $comment->text ?? '' }}"
            data-comment-id="{{ $comment->id ?? null }}"
            data-type-id="{{ \App\Models\CashFlow\Comment::TYPE_PAYMENT }}"
            data-period="{{ $period['format'] }}"
            data-reason="{{ $gr . ';' . $payment->name }}"
            data-object="{{ $payment->object->code ?? 'null' }}"
        >
            <div class="comment-marker {{ $comment ? 'has-comment' : '' }}"></div>
            @if (auth()->user()->can('index cash-flow-plan-payments') && !$payment->from_tax_plan)
                <input
                    type="text"
                    value="{{ $amount }}"
                    class="amount-mask form-control form-control-sm form-control-solid db-field"
                    autocomplete="off"
                    data-payment-id="{{ $payment->id }}"
                    data-date="{{ $period['start'] }}"
                />
            @else
                {{ \App\Models\CurrencyExchangeRate::format(-abs($amount), 'RUB', 0, true) }}
            @endif
        </td>
    @endforeach

    <td class="text-right pe-2">
        {{ \App\Models\CurrencyExchangeRate::format(-abs($total), 'RUB', 0, true) }}
    </td>
</tr>