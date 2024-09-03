@extends('layouts.app')

@section('title', 'Изменение компании ' . $company->name)
@section('toolbar-title', 'Изменение компании')
@section('breadcrumbs', Breadcrumbs::render('companies.edit', $company))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение компании</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('companies.update', $company) }}" method="POST">
                        @csrf
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
                            <div class="row mb-5">
                                <div class="col-md-12 fv-row">
                                    <div class="mb-10 fv-row" data-kt-password-meter="true">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Название</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="name"
                                                    value="{{ old('name', $company->name) }}"
                                                    required
                                                />
                                            </div>
                                            @if ($errors->has('name'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('name')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">Краткая форма названия</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('short_name') ? 'is-invalid' : '' }}"
                                            type="text"
                                            name="short_name"
                                            value="{{ old('short_name', $company->short_name) }}"
                                            required
                                        />
                                        @if ($errors->has('short_name'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('short_name')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">ИНН</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('inn') ? 'is-invalid' : '' }}"
                                            type="text"
                                            name="inn"
                                            value="{{ old('inn', $company->inn) }}"
                                            required
                                        />
                                        @if ($errors->has('inn'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('inn')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-10 fv-row">
                                        <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                        <div class="position-relative mb-3">
                                            <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                @foreach($statuses as $statusId => $status)
                                                    <option value="{{ $statusId }}" {{ $statusId === $company->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ route('companies.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
