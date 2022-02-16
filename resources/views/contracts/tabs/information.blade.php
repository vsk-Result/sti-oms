@extends('contracts.layouts.show')

@section('contract-tab-content')
    <div class="row g-6 g-xl-9 mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <div class="flex-grow-1">

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Компания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->company->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Объект</label>
                                <div class="col-lg-10 fv-row">
                                    @if(auth()->user()->can('show objects'))
                                        <a class="fw-bold fs-6" href="{{ route('objects.contracts.index', $contract->object) }}" class="show-link">{{ $contract->object->getName() }}</a>
                                    @else
                                        <span class="fw-bold text-gray-800 fs-6">{{ $contract->object->getName() }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Номер договора</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Тип договора</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getType() }}</span>
                                </div>
                            </div>

                            @if (! $contract->isMain())
                                <div class="row mb-7">
                                    <label class="col-lg-2 fw-bold text-muted">Основной договор</label>
                                    <div class="col-lg-10 fv-row">
                                        <a class="fw-bold fs-6" href="{{ route('contracts.show', $contract->parent) }}">{{ $contract->parent->getName() }}</a>
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата начала</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getStartDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата окончания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getEndDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Валюта</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->currency }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($contract->isMain())
                                        @foreach($contract->getAmount(false) as $currency => $amount)
                                            <span class="fw-bold text-gray-800 fs-6">{{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}</span>
                                            <br>
                                        @endforeach
                                    @else
                                        <span class="fw-bold text-gray-800 fs-6">{{ \App\Models\CurrencyExchangeRate::format($contract->getAmount(), $contract->currency) }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Авансы</label>
                                <div class="col-lg-10 fv-row">
                                    @forelse($contract->avanses as $avans)
                                        <p class="fw-bold text-gray-800 fs-6">{{ \App\Models\CurrencyExchangeRate::format($avans->getAmount(), $contract->currency) }}</p>
                                    @empty
                                        <span class="fw-bold text-gray-800 fs-6">Отсутствуют</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Полученные авансы</label>
                                <div class="col-lg-10 fv-row">
                                    @forelse($contract->avansesReceived as $avans)
                                        <p class="fw-bold text-gray-800 fs-6">
                                            {{ $avans->getDateFormatted() . ' - ' .  \App\Models\CurrencyExchangeRate::format($avans->getAmount(), $contract->currency) }}
                                            @if ($avans->currency !== 'RUB')
                                                <span class="text-muted fs-7">{{ ' (' . \App\Models\CurrencyExchangeRate::format($avans->getAmountInRUB(), $contract->currency) . ')' }}</span>
                                            @endif
                                        </p>
                                    @empty
                                        <span class="fw-bold text-gray-800 fs-6">Отсутствуют</span>
                                    @endforelse
                                </div>
                            </div>

                            @if (! $contract->isMain())
                                <div class="row mb-7">
                                    <label class="col-lg-2 fw-bold text-muted">Тип суммы</label>
                                    <div class="col-lg-10 fv-row">
                                        <span class="fw-bold text-gray-800 fs-6">{{ $contract->getAmountType() }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Описание</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->description }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Создал</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->createdBy->name }}</span>
                                    <span class="text-muted fw-bold text-muted fs-7">({{ $contract->created_at->format('d/m/Y H:i') }})</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обновил</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($contract->updatedBy)
                                        <span class="fw-bold text-gray-800 fs-6">{{ $contract->updatedBy->name }}</span>
                                        <span class="text-muted fw-bold text-muted fs-7">({{ $contract->updated_at->format('d/m/Y H:i') }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Статус</label>
                                <div class="col-lg-10 fv-row">
                                    @include('partials.status', ['status' => $contract->getStatus()])
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Файлы</label>
                                <div class="col-lg-10 fv-row">
                                    @foreach($contract->getMedia() as $media)
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="svg-icon svg-icon-2x svg-icon-primary me-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="black"></path>
                                                    <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black"></path>
                                                </svg>
                                            </span>
                                            <a target="_blank" href="{{ $media->getUrl() }}" class="text-gray-800 text-hover-primary">{{ $media->file_name . '      (' . $media->human_readable_size . ')' }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
