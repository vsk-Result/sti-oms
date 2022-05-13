<div class="card card-xxl-stretch mb-5 mb-xxl-10">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format(array_sum($balances), 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Баланс на {{ (clone $date)->subDay(1)->format('d.m.Y') }}</span>
        </div>

        @foreach($balances as $bankName => $balance)
            @if ($bankName === 'ПАО "Росбанк"')
                @continue
            @endif
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="fw-bold">{{ $bankName }}</div>

                <div class="d-flex fw-bolder">
                    @if ($bankName === 'ПАО "МКБ"')
                        @php
                            $balance = 2000;
                        @endphp
                    @endif
                    {{ \App\Models\CurrencyExchangeRate::format($balance, 'RUB') }}
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
