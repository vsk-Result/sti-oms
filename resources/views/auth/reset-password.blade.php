@extends('layouts.auth')

@section('content')
    <form class="form w-100" action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="text-center mb-10">
            <h1 class="text-dark mb-3">Установка нового пароля</h1>
            <div class="text-gray-400 fw-bold fs-4">Уже сбросили пароль?
                <a href="{{ route('login') }}" class="link-primary fw-bolder">Войдите</a></div>
        </div>

        <div class="fv-row mb-7">
            <label class="form-label fw-bolder text-dark fs-6">Email</label>
            <input
                class="form-control form-control-lg form-control-solid {{ $errors->has('email') ? 'is-invalid' : '' }}"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
            />
            @if ($errors->has('email'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div>{{ implode(' ', $errors->get('email')) }}</div>
                </div>
            @endif
        </div>

        <div class="mb-10 fv-row" data-kt-password-meter="true">
            <div class="mb-1">
                <label class="form-label fw-bolder text-dark fs-6">Пароль</label>
                <div class="position-relative mb-3">
                    <input
                        class="form-control form-control-lg form-control-solid"
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
                class="form-control form-control-lg form-control-solid  {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
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

        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                <span class="indicator-label">Установить</span>
            </button>
        </div>
    </form>
@endsection
