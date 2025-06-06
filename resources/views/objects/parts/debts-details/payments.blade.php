<div>
        <div class="d-flex flex-row justify-content-between">
            <strong>Наличные расходы</strong>

            <div class="d-flex flex-column align-items-end">
                <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['pay_cash'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['pay_cash_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <strong>Безналичные расходы</strong>
            <div class="d-flex flex-column align-items-end">
                <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['pay_non_cash'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['pay_non_cash_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

    @php
        $transferPayment = $object->transferService()->sum('amount');
    @endphp
        <div class="d-flex flex-row justify-content-between">
            <strong>Трансферные расходы</strong>
            <div class="d-flex flex-column align-items-end">
                <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($transferPayment, 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($transferPayment, 'RUB') }} без НДС</span>
            </div>
        </div>
</div>