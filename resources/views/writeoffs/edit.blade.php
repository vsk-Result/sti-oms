@extends('layouts.app')

@section('toolbar-title', 'Изменение списания')
@section('breadcrumbs', Breadcrumbs::render('writeoffs.edit', $writeoff))

@section('content')
    <div class="post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение списания</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('writeoffs.update', $writeoff) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">

                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">
                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Объект</label>
                                            <div class="position-relative mb-3">
                                                <select name="object_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($objects as $object)
                                                        <option value="{{ $object->id }}" {{ $object->id === $writeoff->object_id ? 'selected' : '' }}>{{ $object->getName() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Компания</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" {{ $company->id === $writeoff->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Дата</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                                        type="text"
                                                        name="date"
                                                        value="{{ old('date', $writeoff->date) }}"
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

                                    <div class="col-md-6 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">UID сотрудника из CRM</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('crm_employee_uid') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="crm_employee_uid"
                                                    value="{{ old('crm_employee_uid', $writeoff->crm_employee_uid) }}"
                                                />
                                            </div>
                                            @if ($errors->has('crm_employee_uid'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('crm_employee_uid')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="amount-mask form-control form-control-lg form-control-solid {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="amount"
                                                    value="{{ old('amount', $writeoff->amount) }}"
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

                                <div class="row">
                                    <div class="col-md-12 mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Описание</label>
                                            <div class="position-relative mb-3">
                                                <textarea
                                                        class="form-control form-control-lg form-control-solid {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                        rows="3"
                                                        name="description"
                                                >{{ old('description', $writeoff->description) }}</textarea>
                                            </div>
                                            @if ($errors->has('description'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('description')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="fs-5 fw-bold mb-2">Файлы</label>
                                        <input
                                                type="file"
                                                multiple
                                                class="form-control form-control-solid {{ $errors->has('files.*') ? 'is-invalid' : '' }}"
                                                placeholder=""
                                                name="files[]"
                                                accept=".jpg, .jpeg, .png, .pdf, .doc, .docx"
                                        />
                                        <div class="form-text">Доступные форматы:
                                            <code>jpg, jpeg, png, pdf, doc, docx</code>
                                        </div>
                                        @if ($errors->has('files.*'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                @foreach($errors->get('files.*') as $message)
                                                    <div>{{ implode(' ', $message) }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                    <div class="position-relative mb-3">
                                        <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                            @foreach($statuses as $statusId => $status)
                                                <option value="{{ $statusId }}" {{ $statusId === $writeoff->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
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
