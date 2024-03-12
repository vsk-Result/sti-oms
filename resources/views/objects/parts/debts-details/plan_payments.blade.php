<div>
    @foreach($object->planPayments as $index => $planPayment)
{{--        @continue($planPayment->amount > -1 && $planPayment->amount < 1)--}}

        <div class="d-flex flex-row justify-content-between">
            <strong>{{ \App\Models\FinanceReport::getNameForField($planPayment->field) }}</strong>
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($planPayment->amount, 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>
    @endforeach
</div>