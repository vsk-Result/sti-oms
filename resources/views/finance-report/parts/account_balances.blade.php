<div class="card card-xxl-stretch mb-5 mb-xxl-10">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($balances['total']['RUB'], 'RUB') }}</div>
        <div class="fs-1 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($balances['total']['EUR'], 'EUR') }}</div>

        <div class="d-flex flex-column fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Баланс на {{ (clone $date)->subDay(1)->format('d.m.Y') }}</span>
            <span class="fs-8">(Последняя выписка загружена {{ \App\Models\PaymentImport::orderByDesc('date')->first()->created_at->format('d.m.Y H:i') }})</span>
        </div>

        @foreach($balances['banks'] as $bankName => $balance)

            @if ($bankName === 'ПАО "Росбанк"')
                @continue
            @endif

            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="fw-bold">{{ $bankName }}</div>

                <div class="d-flex fw-bolder text-end">
                    @if ($bankName === 'ПАО "МКБ"')
                        @php
                            $balance = 11000;
                        @endphp
                        {{ \App\Models\CurrencyExchangeRate::format($balance, 'RUB') }}
                    @else
                        {{ \App\Models\CurrencyExchangeRate::format($balance['RUB'], 'RUB') }}
                        @if ($balance['EUR'] !== 0)
                            <br>
                            {{ \App\Models\CurrencyExchangeRate::format($balance['EUR'], 'EUR') }}
                        @endif
                    @endif
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
