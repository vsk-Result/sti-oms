<div>
    <div class="row">
        @foreach($workSalaryDebtDetails as $detail)
            <div class="col-md-6 mb-4">
                <strong class="{{ $detail['is_real'] ? '' : 'required' }}">{{ $detail['date'] }}</strong>
                <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($detail['amount'], 'RUB') }}</span>
            </div>
        @endforeach
    </div>
    <div class="predict text-muted"><span class="required"></span> прогнозируемый долг</div>
</div>