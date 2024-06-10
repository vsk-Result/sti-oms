<div>
        <div class="d-flex flex-row justify-content-between">
            <strong>Заказчик</strong>
            <span class="text-success"><strong>{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer'], 'RUB') }}</strong></span>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Фиксированный аванс</p>
            <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_fix_avans'], 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Целевой аванс</p>
            <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_target_avans'], 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Акты</p>
            <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_acts'], 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <p>&nbsp;&nbsp; Гарантийное удержание</p>
            <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_customer_gu'], 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <strong>Ретробонусы ДТГ</strong>
            <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_retro_dtg'], 'RUB') }}</span>
        </div>

        <div class="separator separator-dashed my-3"></div>

        <div class="d-flex flex-row justify-content-between">
            <strong>Прочие</strong>
            <span class="text-success">{{ \App\Models\CurrencyExchangeRate::format($info['receive_other'], 'RUB') }}</span>
        </div>
</div>