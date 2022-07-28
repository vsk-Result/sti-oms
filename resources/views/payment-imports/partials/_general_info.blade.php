<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Тип</label>
    <div class="col-lg-10 fv-row">
        <span class="fw-bold text-gray-800 fs-6">{{ $import->getType() }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Дата</label>
    <div class="col-lg-10 fv-row">
        <span class="fw-bold text-gray-800 fs-6">{{ $import->getDateFormatted() }}</span>
    </div>
</div>

@if ($import->isStatement())
    @inject('currencyExchangeService', 'App\Services\CurrencyExchangeRateService')
    @php
        $USDExchangeRate = $currencyExchangeService->getExchangeRate($import->date, 'USD');
        $EURExchangeRate = $currencyExchangeService->getExchangeRate($import->date, 'EUR');
    @endphp

    <div class="row mb-7">
        <label class="col-lg-2 fw-bold text-muted">Валюта</label>
        <div class="col-lg-10 fv-row">
            <div class="fw-bold text-gray-800 fs-6">{{ $import->currency }}</div>
        </div>
    </div>

    @if ($USDExchangeRate && $EURExchangeRate)
        <div class="row mb-7">
            <label class="col-lg-2 fw-bold text-muted">Курс валют</label>
            <div class="col-lg-10 fv-row">
                <div class="fw-bold text-gray-800 fs-6">USD: <span class="fw-bolder">{{ $USDExchangeRate->rate }}</span>, EUR: <span class="fw-bolder">{{ $EURExchangeRate->rate }}</span></div>
            </div>
        </div>
    @endif
@endif

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Компания</label>
    <div class="col-lg-10 fv-row">
        <span class="fw-bold text-gray-800 fs-6">{{ $import->company->name }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Банк</label>
    <div class="col-lg-10 fv-row">
        <span class="fw-bold text-gray-800 fs-6">{{ $import->getBankName() }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Оплат</label>
    <div class="col-lg-10 fv-row">
        <span class="fw-bold text-gray-800 fs-6">{{ $import->payments_count }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Входящий остаток</label>
    <div class="col-lg-10 fv-row">
        <span class="{{ $import->incoming_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $import->getIncomingBalance() }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Расход</label>
    <div class="col-lg-10 fv-row">
        <span class="text-danger fw-bold fs-6">{{ $import->getAmountPay() }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Приход</label>
    <div class="col-lg-10 fv-row">
        <span class="text-success fw-bold fs-6">{{ $import->getAmountReceive() }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Исходящий остаток</label>
    <div class="col-lg-10 fv-row">
        <span class="{{ $import->outgoing_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $import->getOutgoingBalance() }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Описание</label>
    <div class="col-lg-10 fv-row">
        <span class="fw-bold text-gray-800 fs-6">{{ $import->description }}</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Загрузил</label>
    <div class="col-lg-10 fv-row">
        <span class="fw-bold text-gray-800 fs-6">{{ $import->createdBy->name }}</span>
        <span class="text-muted fw-bold text-muted fs-7">({{ $import->created_at->format('d/m/Y H:i') }})</span>
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Обновил</label>
    <div class="col-lg-10 fv-row">
        @if ($import->updatedBy)
            <span class="fw-bold text-gray-800 fs-6">{{ $import->updatedBy->name }}</span>
            <span class="text-muted fw-bold text-muted fs-7">({{ $import->updated_at->format('d/m/Y H:i') }})</span>
        @endif
    </div>
</div>

<div class="row mb-7">
    <label class="col-lg-2 fw-bold text-muted">Статус</label>
    <div class="col-lg-10 fv-row">
        @include('partials.status', ['status' => $import->getStatus()])
    </div>
</div>
