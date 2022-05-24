@extends('layouts.app')

@section('toolbar-title', 'Новый объект')
@section('breadcrumbs', Breadcrumbs::render('objects.create'))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Новый объект</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('objects.store') }}" method="POST">
                        @csrf
                        <div class="row mb-5">
                                <div class="col-md-12 fv-row">
                                    <div class="mb-7 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Код</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="code"
                                                    value="{{ old('code') }}"
                                                    required
                                                    autofocus
                                                />
                                            </div>
                                            @if ($errors->has('code'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('code')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-check form-check-custom form-check-solid mb-6 fw-bold fs-6">
                                        <input name="is_without_worktype" class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                        <label class="form-check-label" for="flexCheckChecked">Без вида работ</label>
                                    </div>

                                    <div class="mb-10 fv-row">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Название</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                    type="text"
                                                    name="name"
                                                    value="{{ old('name') }}"
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
                                        <label class="form-label fw-bolder text-dark fs-6">Адрес</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('address') ? 'is-invalid' : '' }}"
                                            type="text"
                                            value="{{ old('address') }}"
                                            name="address"
                                        />
                                        @if ($errors->has('address'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('address')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">ФИО ответственного</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('responsible_name') ? 'is-invalid' : '' }}"
                                            type="text"
                                            value="{{ old('responsible_name') }}"
                                            name="responsible_name"
                                        />
                                        @if ($errors->has('responsible_name'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('responsible_name')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">Email ответственного</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('responsible_email') ? 'is-invalid' : '' }}"
                                            type="email"
                                            value="{{ old('responsible_email') }}"
                                            name="responsible_email"
                                        />
                                        @if ($errors->has('responsible_email'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('responsible_email')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">Телефон ответственного</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('responsible_phone') ? 'is-invalid' : '' }}"
                                            type="text"
                                            value="{{ old('responsible_phone') }}"
                                            name="responsible_phone"
                                        />
                                        @if ($errors->has('responsible_phone'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('responsible_phone')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">Заказчики</label>
                                        <select name="customer_id[]" class="form-select form-select-solid" data-control="select2" multiple>
                                            @foreach($organizations as $organization)
                                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">Дата закрытия</label>
                                        <input
                                            class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('closing_date') ? 'is-invalid' : '' }}"
                                            type="text"
                                            value="{{ old('closing_date') }}"
                                            name="closing_date"
                                            readonly
                                        />
                                        @if ($errors->has('closing_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('closing_date')) }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="form-label fw-bolder text-dark fs-6">Фотография</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                                            type="file"
                                            name="photo"
                                            accept=".jpg,.jpeg,.png"
                                        />
                                        <div class="form-text">Доступные форматы:
                                            <code>png, jpg, jpeg</code>
                                        </div>
                                        @if ($errors->has('photo'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('photo')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Создать</span>
                            </button>
                            <a href="{{ route('objects.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
