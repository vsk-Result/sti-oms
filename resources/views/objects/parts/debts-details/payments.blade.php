<div>
        <div class="d-flex flex-row justify-content-between">
            <strong>Наличные расходы</strong>
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['pay_cash'], 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <strong>Безналичные расходы</strong>
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['pay_non_cash'], 'RUB') }}</span>
        </div>
</div>