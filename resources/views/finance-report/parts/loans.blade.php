<div class="card card-xxl-stretch mb-5 mb-xxl-10">
    <div class="card-body p-9">

        <div class="fs-1 fw-bolder {{ array_sum($loans) < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format(array_sum($loans), 'RUB') }}</div>

        <div class="d-flex align-items-center fs-5 fw-bold text-gray-400 mb-7">
            <span class="d-flex">Долг по займам на {{ $date->format('d.m.Y') }}</span>
        </div>

        @foreach($loans as $orgName => $loan)
            <div class="fs-6 d-flex justify-content-between my-4">
                <div class="fw-bold">{{ $orgName }}</div>

                <div class="d-flex fw-bolder {{ $loan < 0 ? 'text-danger' : 'text-success' }}">
                    {{ \App\Models\CurrencyExchangeRate::format($loan, 'RUB') }}
                </div>
            </div>

            @if (! $loop->last)
                <div class="separator separator-dashed"></div>
            @endif
        @endforeach
    </div>
</div>
