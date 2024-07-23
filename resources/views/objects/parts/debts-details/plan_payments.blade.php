<div>
    @foreach($object->planPayments as $index => $planPayment)
{{--        @continue($planPayment->amount > -1 && $planPayment->amount < 1)--}}
        @continue(empty(\App\Models\FinanceReport::getNameForField($planPayment->field)))
        @continue($planPayment->field === 'prognoz_consalting' && $planPayment->amount == 0)

        <div class="d-flex flex-row justify-content-between">
            <strong>
                @if ($planPayment->field === 'prognoz_general')
                    {{ 'Общие расходы (' . number_format(abs($summ['general_balance_to_receive_percentage']), 2) . '%)' }}
                @else
                    {{ \App\Models\FinanceReport::getNameForField($planPayment->field) }}
                @endif
            </strong>
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($planPayment->amount, 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>
    @endforeach
</div>