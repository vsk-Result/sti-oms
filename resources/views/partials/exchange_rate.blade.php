<div class="d-flex ms-1 ms-lg-2 flex-column justify-content-center me-8">

    @if ($USDExchangeRate && $EURExchangeRate)
        <div class="d-flex align-items-center justify-content-between" style="{{ $needFlag ? 'margin-left: 2.2rem;' : '' }}">
            <div class="fs-7 text-muted">
                {{ $date->format('d.m.Y') }}
            </div>
        </div>

        <div class="d-flex align-items-center">
            @if ($needFlag)
                <div class="me-4"><img width="18" src="{{ asset('images/flags/united-states.png') }}" alt="Доллар США" /></div>
            @endif
            <div class="fs-6 fw-bold me-2">{{ $USDExchangeRate->rate }}</div>
            @if ($USDExchangeRate->diff_rate > 0)
                <div class="fs-8 text-danger">+{{ $USDExchangeRate->diff_rate }}</div>
            @else
                <div class="fs-8 text-success">{{ $USDExchangeRate->diff_rate }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center">
            @if ($needFlag)
                <div class="me-4"><img width="19" src="{{ asset('images/flags/european-union.png') }}" alt="Евро" /></div>
            @endif
            <div class="fs-6 fw-bold me-2">{{ $EURExchangeRate->rate }}</div>
            @if ($EURExchangeRate->diff_rate > 0)
                <div class="fs-8 text-danger">+{{ $EURExchangeRate->diff_rate }}</div>
            @else
                <div class="fs-8 text-success">{{ $EURExchangeRate->diff_rate }}</div>
            @endif
        </div>
    @endif
</div>
