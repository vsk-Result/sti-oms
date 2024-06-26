<div>
        <div class="d-flex flex-row justify-content-between">
            <strong>Заказчик</strong>

            <div class="d-flex flex-column align-items-end">
                <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Фиксированный аванс</p>

            <div class="d-flex flex-column align-items-end">
                <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_fix_avans'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_fix_avans_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Целевой аванс</p>

            <div class="d-flex flex-column align-items-end">
                <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_target_avans'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_target_avans_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Акты</p>

            <div class="d-flex flex-column align-items-end">
                <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_acts'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_acts_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Гарантийное удержание</p>

            <div class="d-flex flex-column align-items-end">
                <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_gu'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_gu_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <strong>Ретробонусы ДТГ</strong>

            <div class="d-flex flex-column align-items-end">
                <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_retro_dtg'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['receive_retro_dtg_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <strong>Прочие</strong>

            <div class="d-flex flex-column align-items-end">
                <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_other'], 'RUB') }}</span>
                <span class="text-muted fw-bold d-block fs-8">{{ \App\Models\CurrencyExchangeRate::format($info['receive_other_without_nds'], 'RUB') }} без НДС</span>
            </div>
        </div>
</div>