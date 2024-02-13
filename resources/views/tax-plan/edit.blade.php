@extends('layouts.app')

@section('toolbar-title', 'Изменение записи')
@section('breadcrumbs', Breadcrumbs::render('tax_plan.edit', $item))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение записи</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('tax_plan.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                                <div class="position-relative mb-3">
                                                    <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                        @foreach($companies as $company)
                                                            <option value="{{ $company->id }}" {{ $company->id === $item->company_id ? 'selected' : '' }}>{{ $company->short_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Наименование</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                            class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="name"
                                                            value="{{ old('name', $item->name) }}"
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
                                                        value="{{ old('amount', $item->amount) }}"
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
                                                        value="{{ old('period', $item->period) }}"
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
                                                        value="{{ old('due_date', $item->due_date) }}"
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
                                                <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                                                <div class="position-relative mb-3">
                                                    <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                        <option value="{{ null }}" {{ null == $item->object_id ? 'selected' : '' }}>Не указан</option>
                                                        @foreach($objects as $object)
                                                            <option value="{{ $object->id }}" {{ $object->id == $item->object_id ? 'selected' : '' }}>{{ $object->getName() }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                                <div class="position-relative mb-3">
                                                    <select name="paid" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                        @foreach(['Не оплачено', 'Оплачено'] as $index => $value)
                                                            <option value="{{ $index }}" {{ $item->paid === $index ? 'selected' : '' }}>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-10 fv-row">
                                            <div class="mb-1">
                                                <label class="form-label fw-bolder text-dark fs-6">Оплачено</label>
                                                <div class="position-relative mb-3">
                                                    <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('payment_date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="payment_date"
                                                        value="{{ old('payment_date', $item->payment_date) }}"
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

                                        <input
                                            type="hidden"
                                            name="status_id"
                                            value="{{ old('status_id', $item->status_id) }}"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

