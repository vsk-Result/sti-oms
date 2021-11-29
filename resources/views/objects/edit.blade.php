@extends('layouts.app')

@section('toolbar-title', 'Изменение объекта')
@section('breadcrumbs', Breadcrumbs::render('objects.edit', $object))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение объекта</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('objects.update', $object) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">
                                <div class="mb-10 fv-row" data-kt-password-meter="true">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Код</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="code"
                                                value="{{ old('code', $object->code) }}"
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

                                <div class="mb-10 fv-row" data-kt-password-meter="true">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Название</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="name"
                                                value="{{ old('name', $object->name) }}"
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
                                        value="{{ old('address', $object->address) }}"
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
                                        value="{{ old('responsible_name', $object->responsible_name) }}"
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
                                        value="{{ old('responsible_email', $object->responsible_email) }}"
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
                                        value="{{ old('responsible_phone', $object->responsible_phone) }}"
                                        name="responsible_phone"
                                    />
                                    @if ($errors->has('responsible_phone'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div>{{ implode(' ', $errors->get('responsible_phone')) }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="col-lg-4 col-form-label fw-bold fs-6">Фотография</label>
                                    <div class="col-lg-8">
                                        <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url({{ asset('images/blanks/object_photo_blank.jpg') }})">
                                            <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $object->getPhoto() }})"></div>
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Изменить фото">
                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="avatar_remove" />
                                            </label>
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Отменить фото">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                                            @if ($object->photo)
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Удалить фото">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                                            @endif
                                        </div>
                                        <div class="form-text">Поддерживаются форматы: <code>png, jpg, jpeg</code></div>
                                        @if ($errors->has('photo'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('photo')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                    <div class="position-relative mb-3">
                                        <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                            @foreach($statuses as $statusId => $status)
                                                <option value="{{ $statusId }}" {{ $statusId === $object->status_id ? 'selected' : '' }}>{{ $status }}</option>
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
                            <a href="{{ route('objects.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
