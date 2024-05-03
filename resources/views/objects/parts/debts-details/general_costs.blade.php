<div>
    <div class="d-flex flex-row justify-content-between">
        <strong>Общие затраты</strong>
        <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['general_balance'] - $info['transfer_service'], 'RUB') }}</span>
    </div>

    <div class="separator separator-dashed my-3"></div>

    <div class="d-flex flex-row justify-content-between">
        <strong>Услуги по трансферу</strong>
        <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['transfer_service'], 'RUB') }}</span>
    </div>
</div>