@extends('layouts.app')

@section('toolbar-title', 'Изменение пароля')
@section('breadcrumbs', Breadcrumbs::render('users.passwords.reset', auth()->user()))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Измение пароля</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('users.passwords.reset.update', $user) }}" method="POST">
                        @csrf
                        <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_new_address_header" data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
                            <div class="row mb-5">
                                <div class="col-md-12 fv-row">
                                    <div class="mb-10 fv-row" data-kt-password-meter="true">
                                        <div class="mb-1">
                                            <label class="form-label fw-bolder text-dark fs-6">Пароль</label>
                                            <div class="position-relative mb-3">
                                                <input
                                                    class="form-control form-control-lg form-control-solid {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                                    type="password"
                                                    name="password"
                                                    required
                                                />
                                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                                            </div>
                                            <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                                            </div>
                                            @if ($errors->has('password'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div>{{ implode(' ', $errors->get('password')) }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-muted">Не менее 8 символов разного регистра, используя буквы, числа и символы.</div>
                                    </div>

                                    <div class="fv-row mb-5">
                                        <label class="form-label fw-bolder text-dark fs-6">Подтверждение пароля</label>
                                        <input
                                            class="form-control form-control-lg form-control-solid {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                        />
                                        @if ($errors->has('password_confirmation'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('password_confirmation')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ route('users.edit', $user) }}" id="kt_modal_new_address_cancel" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
