@extends('layouts.app')

@section('toolbar-title', 'Новая запись')
@section('breadcrumbs', Breadcrumbs::render('tax_plan.create'))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новая запись</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('tax_plan.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">
                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Наименование</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="name"
                                                value="{{ old('name') }}"
                                                required
                                                autofocus
                                            />
                                        </div>
                                        @if ($errors->has('name'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('name')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 fv-row">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="amount"
                                                        value="{{ old('amount') }}"
                                                        required
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

                                    <div class="col-md-4 fv-row">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Период</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="form-control form-control-lg form-control-solid {{ $errors->has('period') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="period"
                                                        value="{{ old('period') }}"
                                                    />
                                                </div>
                                                @if ($errors->has('period'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('period')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 fv-row">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Срок оплаты</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('due_date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="due_date"
                                                        value="{{ old('due_date') }}"
                                                        readonly
                                                    />
                                                </div>
                                                @if ($errors->has('due_date'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('due_date')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Платежка в 1С</label>
                                                <div class="position-relative mb-3">
                                                    <select name="in_one_c" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                        @foreach(['Нет', 'Да'] as $index => $value)
                                                            <option value="{{ $index }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Дата оплаты</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('payment_date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="payment_date"
                                                        value="{{ old('payment_date') }}"
                                                        readonly
                                                    />
                                                </div>
                                                @if ($errors->has('payment_date'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div>{{ implode(' ', $errors->get('payment_date')) }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

