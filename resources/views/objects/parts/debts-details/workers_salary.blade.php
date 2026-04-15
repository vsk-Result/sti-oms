{{--<div>--}}
{{--    <div class="row">--}}
{{--        @foreach($workSalaryDebtDetails as $detail)--}}
{{--            <div class="col-md-6 mb-4">--}}
{{--                <strong class="{{ $detail['is_real'] ? '' : 'required' }}">{{ $detail['date'] }}</strong>--}}
{{--                <span class="{{ $detail['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($detail['amount'], 'RUB') }}</span>--}}
{{--            </div>--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--    <div class="predict text-muted"><span class="required"></span> прогнозируемый долг</div>--}}
{{--</div>--}}

<div>
    <div class="row">
        @foreach($workSalaryDebtDetails as $detail)
            <div class="col-md-6 mb-4">
                <strong>{{ $detail['formatted_date'] }}</strong>
                <span class="{{ $detail['amount'] > 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($detail['amount']) }}</span>
            </div>
        @endforeach
    </div>
</div>