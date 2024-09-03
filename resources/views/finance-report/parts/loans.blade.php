<div class="card card-xxl-stretch mb-5 mb-xxl-10">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder {{ $loansInfo->totalLoanAmount < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($loansInfo->totalLoanAmount, 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Долг по займам на {{ $loansInfo->loansLastUpdateDate }}</span>
        </div>

        @foreach($loansInfo->loans as $loan)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div>
                    <div class="fw-bold">{{ $loan->organization->name }}</div>
                    <p class="fs-7">
                        <a
                            class="text-muted"
                            style="border-bottom: 1px dashed #ccc;"
                            href="{{ route('loans.history.index', $loan->id) }}"
                        >
                            {{ $loan->name }}
                        </a>
                    </p>
                </div>

                <div class="min-w-100px d-flex fw-bolder {{ $loan->amount < 0 ? 'text-danger' : 'text-success' }}">
                    {{ \App\Models\CurrencyExchangeRate::format($loan->amount, 'RUB') }}
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
