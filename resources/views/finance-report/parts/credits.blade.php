<div class="card card-xxl-stretch mb-5 mb-xxl-10">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder {{ $totalCreditAmount < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($totalCreditAmount, 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Долг по кредитам на {{ $date->format('d.m.Y') }}</span>
        </div>

        @foreach($credits as $credit)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="d-flex flex-column">
                    <div class="fw-bold">
                        {{ $credit['bank'] }}
                    </div>
                    <p class="fs-7">
                        <a
                            target="_blank"
                            class="text-muted"
                            style="border-bottom: 1px dashed #ccc;"
                            href="{{ route('payments.index') }}?bank_id%5B%5D={{ $credit['bank_id'] }}&description={{ str_replace(' ', '+', $credit['contract']) }}"
                        >
                            {{ $credit['contract'] }}
                        </a>
                    </p>
                </div>

                <div>
                    <div class="d-flex">
                        <div class="d-flex flex-column me-8">
                            <span class="text-muted fs-8">Доступно</span>
                            <span class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($credit['amount'] - $credit['received'], 'RUB') }}</span>
                        </div>
                        <div class="d-flex flex-column me-8">
                            <span class="text-muted fs-8">В использовании</span>
                            <span class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format(abs($credit['sent']), 'RUB') }}</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-muted fs-8">Всего</span>
                            <span class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($credit['amount'], 'RUB') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
