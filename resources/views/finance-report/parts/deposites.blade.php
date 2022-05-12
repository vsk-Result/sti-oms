<div class="card">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($depositesAmount, 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Баланс по депозитам на {{ $date->format('d.m.Y') }}</span>
        </div>

        @foreach($deposites as $currency => $deposit)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="fw-bold">{{ $currency }}</div>

                <div class="d-flex fw-bolder">
                    <a class="text-success" target="_blank" style="border-bottom: 1px dashed #ccc;" href="{{ route('bank_guarantees.index') }}?currency%5B%5D={{ $currency }}">
                        {{ \App\Models\CurrencyExchangeRate::format($deposit, $currency) }}
                    </a>
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
