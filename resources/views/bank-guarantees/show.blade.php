@extends('layouts.app')

@section('toolbar-title', 'Банковская гарантия #' . $guarantee->id)
@section('breadcrumbs', Breadcrumbs::render('bank_guarantees.show', $guarantee))

@section('content')
    <div class="post">
        <div class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Банковская гарантия #{{ $guarantee->id }}</span>
                    </h3>

                    <div class="card-toolbar">
                        @can('edit bank-guarantees')
                            <a href="{{ route('bank_guarantees.edit', $guarantee) }}" class="btn btn-sm btn-primary me-3">Изменить</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <div class="flex-grow-1">
                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Компания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->company->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Банк</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->getBankName() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Объект</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->object->getName() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обеспечение</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->target }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата начала</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->getStartDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата окончания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->getEndDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->getAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата начала депозита</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->getStartDateDepositFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата окончания депозита</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->getEndDateDepositFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма депозита</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->getAmountDeposit() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Создал</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->createdBy->name }}</span>
                                    <span class="text-muted fw-bold text-muted fs-7">({{ $guarantee->created_at->format('d/m/Y H:i') }})</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обновил</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($guarantee->updatedBy)
                                        <span class="fw-bold text-gray-800 fs-6">{{ $guarantee->updatedBy->name }}</span>
                                        <span class="text-muted fw-bold text-muted fs-7">({{ $guarantee->updated_at->format('d/m/Y H:i') }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Статус</label>
                                <div class="col-lg-10 fv-row">
                                    @include('partials.status', ['status' => $guarantee->getStatus()])
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Файлы</label>
                                <div class="col-lg-10 fv-row">
                                    @foreach($guarantee->getMedia() as $media)
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

                    <div class="d-flex flex-center py-3">
                        <a href="{{ route('bank_guarantees.index') }}" class="btn btn-light">Отменить</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
