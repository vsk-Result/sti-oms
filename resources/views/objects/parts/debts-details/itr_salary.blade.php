<div>
    <div class="row">
        @foreach($itrSalaryDebtDetails as $detail)
            <div class="col-md-6 mb-4">
                <strong>{{ $detail['formatted_date'] }}</strong>
                <span class="{{ $detail['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($detail['amount']) }}</span>
            </div>
        @endforeach
    </div>
</div>