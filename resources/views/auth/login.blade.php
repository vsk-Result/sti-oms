@extends('layouts.auth')

@section('content')
    <form class="form w-100" action="{{ route('login') }}" method="POST">
        @csrf

        <div class="text-center mb-10">
            <h1 class="text-dark mb-3">Вход</h1>
            <div class="text-gray-400 fw-bold fs-4">Нет аккаунта?
                <a href="{{ route('register') }}" class="link-primary fw-bolder">Зарегистрируйтесь</a></div>
        </div>

        <div class="fv-row mb-10">
            <label class="form-label fs-6 fw-bolder text-dark">Email</label>
            <input
                class="form-control form-control-lg form-control-solid {{ $errors->has('email') ? 'is-invalid' : '' }}"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
            />
            @if ($errors->has('email'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div>{{ implode(' ', $errors->get('email')) }}</div>
                </div>
            @endif
        </div>

        <div class="fv-row mb-10">
            <div class="d-flex flex-stack mb-2">
                <label class="form-label fw-bolder text-dark fs-6 mb-0">Пароль</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link-primary fs-6 fw-bolder">
                        Забыли пароль?
                    </a>
                @endif
            </div>
            <input
                class="form-control form-control-lg form-control-solid {{ $errors->has('password') ? 'is-invalid' : '' }}"
                type="password"
                name="password"
                required
            />
            @if ($errors->has('password'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div>{{ implode(' ', $errors->get('password')) }}</div>
                </div>
            @endif
        </div>

        <div class="fv-row mb-10">
            <label class="form-check form-check-custom form-check-solid form-check-inline">
                <input class="form-check-input" type="checkbox" name="remember" checked />
                <span class="form-check-label fw-bold text-gray-700 fs-6">Запомнить</span>
            </label>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                <span class="indicator-label">Войти</span>
            </button>
        </div>
    </form>
@endsection
