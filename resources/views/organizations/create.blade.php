@extends('layouts.app')

@section('toolbar-title', 'Новая организация')
@section('breadcrumbs', Breadcrumbs::render('organizations.create'))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новая организация</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('organizations.store') }}" method="POST">
                        @csrf
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
                            <div class="row mb-5">
                                <div class="col-md-12 fv-row">
                                    <div class="mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Связать с компанией</label>
                                            <div class="position-relative mb-3">
                                                <select name="company_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                    <option value="null">Отсутствует</option>
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Название</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="name"
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

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">ИНН</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('inn') ? 'is-invalid' : '' }}"
                                            type="text"
                                            name="inn"
                                        />
                                        @if ($errors->has('inn'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('inn')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">КПП</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('kpp') ? 'is-invalid' : '' }}"
                                            type="text"
                                            name="kpp"
                                        />
                                        @if ($errors->has('kpp'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('kpp')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-10 fv-row">
                                        <label class="form-label fw-bolder text-dark fs-6">Тип расчета НДС</label>
                                        <div class="position-relative mb-3">
                                            <select name="nds_status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                                @foreach($NDSStatuses as $statusId => $status)
                                                    <option value="{{ $statusId }}">{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ route('organizations.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
