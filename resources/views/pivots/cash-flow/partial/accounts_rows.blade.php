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