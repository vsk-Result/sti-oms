<div class="card card-xxl-stretch mb-5 mb-xxl-10">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder {{ $creditsInfo->totalCreditAmount < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($creditsInfo->totalCreditAmount, 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Долг по кредитам на {{ $creditsInfo->creditsLastUpdateDate }}</span>
        </div>

        @foreach($creditsInfo->credits as $credit)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="d-flex flex-column">
                    <div class="fw-bold">
                        {{ $credit->bank }}
                    </div>
                    <p class="fs-7">
                        <a
                            class="text-muted"
                            style="border-bottom: 1px dashed #ccc;"
                            href="{{ route('loans.history.index', $credit->id) }}"
                        >
                            {{ $credit->contract }}
                        </a>
                    </p>
                </div>

                <div>
                    @if (isset($credit->credit_type_id) && $credit->credit_type_id === \App\Models\Loan::CREDIT_TYPE_DEFAULT)
                        <div class="d-flex">
                            <div class="d-flex flex-column me-8">
                                <span class="text-muted fs-8">Сумма кредита</span>
                                <span class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($credit->total, 'RUB') }}</span>
                            </div>
                            <div class="d-flex flex-column me-8">
                                <span class="text-muted fs-8">Погашено</span>
                                <span class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($credit->paid ?? 0, 'RUB') }}</span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-muted fs-8">Остаток кредита</span>
                                <span class="fw-bolder text-danger">{{ \App\Models\CurrencyExchangeRate::format(abs($credit->debt), 'RUB') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="d-flex">
                            <div class="d-flex flex-column me-8">
                                <span class="text-muted fs-8">Всего</span>
                                <span class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($credit->total, 'RUB') }}</span>
                            </div>
                            <div class="d-flex flex-column me-8">
                                <span class="text-muted fs-8">В использовании</span>
                                <span class="fw-bolder text-danger">{{ \App\Models\CurrencyExchangeRate::format($credit->paid ?? 0, 'RUB') }}</span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-muted fs-8">Доступно</span>
                                <span class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($credit->total - $credit->paid ?? 0, 'RUB') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
