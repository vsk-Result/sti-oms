<div class="card">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($depositsInfo->totalDepositsAmount, 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Баланс по депозитам на {{ $date->format('d.m.Y') }}</span>
        </div>

        @foreach($depositsInfo->deposits as $name => $deposit)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="fw-bold">{{ $name }}</div>

                <div class="d-flex fw-bolder">
                    <a class="text-success" style="border-bottom: 1px dashed #ccc;" href="{{ $name === 'Депозиты' ? route('deposits.index') : route('bank_guarantees.index') }}">
                        {{ \App\Models\CurrencyExchangeRate::format($deposit, 'RUB') }}
                    </a>
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
