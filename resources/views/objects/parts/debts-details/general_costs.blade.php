<div>
    <div class="d-flex flex-row justify-content-between">
        <strong>Услуги по трансферу</strong>

        <div class="d-flex flex-column align-items-end">
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['transfer_service'], 'RUB') }}</span>
{{--            <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['transfer_service_without_nds'], 'RUB') }} без НДС</span>--}}
        </div>
    </div>

    <div class="separator separator-dashed my-3"></div>

    <div class="d-flex flex-row justify-content-between">
        <strong>Расходы центрального офиса</strong>

        <div class="d-flex flex-column align-items-end">
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['office_service'], 'RUB') }}</span>
{{--            <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['office_service_without_nds'], 'RUB') }} без НДС</span>--}}
        </div>
    </div>

    <div class="separator separator-dashed my-3"></div>

    <div class="d-flex flex-row justify-content-between">
        <strong>НДС, налог на прибыль</strong>

        <div class="d-flex flex-column align-items-end">
            <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['general_nds'] ?? 0, 'RUB') }}</span>
{{--            <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['office_service_without_nds'], 'RUB') }} без НДС</span>--}}
        </div>
    </div>
</div>