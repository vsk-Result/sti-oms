<div>
        <div class="d-flex flex-row justify-content-between">
            <strong>Фиксированная часть контракта</strong>

            <div class="d-flex flex-column align-items-end">
                <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['provider_debt_fix'], 'RUB') }}</span>
{{--                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['provider_debt_fix_without_nds'], 'RUB') }} без НДС</span>--}}
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <strong>Изменяемая часть контракта</strong>
            <div class="d-flex flex-column align-items-end">
                <span class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($info['provider_debt_float'], 'RUB') }}</span>
{{--                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['provider_debt_float_without_nds'], 'RUB') }} без НДС</span>--}}
            </div>
        </div>
</div>