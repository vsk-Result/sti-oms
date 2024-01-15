<div>
    <div class="row">
        <div class="col-md-6">
            <strong>{{ $workSalaryDebtDetails['real']['date'] }}</strong>
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($workSalaryDebtDetails['real']['amount'], 'RUB') }}</span>
        </div>
        <div class="col-md-6">
            <strong class="required">{{ $workSalaryDebtDetails['predict']['date'] }}</strong>
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($workSalaryDebtDetails['predict']['amount'], 'RUB') }}</span>
        </div>
    </div>
    <div class="predict text-muted"><span class="required"></span> прогнозируемый долг</div>
</div>