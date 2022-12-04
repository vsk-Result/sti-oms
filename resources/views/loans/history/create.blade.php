@extends('layouts.app')

@section('toolbar-title', 'Новая оплата для ' . $loan->name . ' (' . $loan->getType() . ')')
@section('breadcrumbs', Breadcrumbs::render('loans.history.create', $loan))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">{{ 'Новая оплата для ' . $loan->name . ' (' . $loan->getType() . ')' }}</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('loans.history.store', $loan) }}?return_url={{ request()->get('return_url', '') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">

                                <div class="row">
                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата оплаты</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="date"
                                                        value="{{ old('date') }}"
                                                        readonly
                                                />
                                            </div>
                                            @if ($errors->has('date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Планируемая дата возврата</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('planned_refund_date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="planned_refund_date"
                                                        value="{{ old('planned_refund_date') }}"
                                                        readonly
                                                />
                                            </div>
                                            @if ($errors->has('planned_refund_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('planned_refund_date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата возврата</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('refund_date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="refund_date"
                                                        value="{{ old('refund_date') }}"
                                                        readonly
                                                />
                                            </div>
                                            @if ($errors->has('refund_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('refund_date')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                            class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="amount"
                                                            value="{{ old('amount') }}"
                                                    />
                                                </div>
                                                @if ($errors->has('amount'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('amount')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Процент</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                            class="form-control form-control-lg form-control-solid {{ $errors->has('percent') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="percent"
                                                            value="{{ old('percent') }}"
                                                    />
                                                </div>
                                                @if ($errors->has('percent'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('percent')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                                        <div class="position-relative mb-3">
                                            <textarea
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                    name="description"
                                            >{{ old('description') }}</textarea>
                                        </div>
                                        @if ($errors->has('description'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('description')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ request()->get('return_url') ?? route('loans.history.index', $loan) }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
