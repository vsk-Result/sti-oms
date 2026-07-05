@if (count($currentPayments) > 0)
    <p>Плановые оплаты на текущую неделю:</p>

    <ul>
        @foreach($currentPayments as $paymentName => $amount)
            <li><strong>{{ $paymentName }}</strong> - на сумму <strong style="color: red;">{{ \App\Models\CurrencyExchangeRate::format($amount) }}</strong></li>
        @endforeach
    </ul>

    <hr />
@else
    <p>Плановых оплат на текущую неделю нет</p>
    <hr />
@endif

@if (count($nextPayments) > 0)
    <p>Плановые оплаты на следующую неделю:</p>

    <ul>
        @foreach($nextPayments as $paymentName => $amount)
            <li><strong>{{ $paymentName }}</strong> - на сумму <strong style="color: red;">{{ \App\Models\CurrencyExchangeRate::format($amount) }}</strong></li>
        @endforeach
    </ul>
@else
    <p>Плановых оплат на следующую неделю нет</p>
@endif