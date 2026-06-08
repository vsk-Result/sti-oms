<p>Уважаемые коллеги.</p>

@if (count($payments) > 0)
    <p>Плановые оплаты на следующую неделю:</p>

    <ul>
        @foreach($payments as $paymentName => $amount)
            <li><strong>{{ $paymentName }}</strong> - на сумму <strong style="color: red;"{{ \App\Models\CurrencyExchangeRate::format($amount) }}</li>
        @endforeach
    </ul>
@else
    <p>Плановых оплат на следующую неделю нет</p>
@endif

<p>Хорошего дня!</p>