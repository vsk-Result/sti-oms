@extends('layouts.auth')

@section('content')
    <form class="form w-100" action="{{ route('password.confirm') }}" method="POST">
        @csrf

        <div class="text-center mb-10">
            <h1 class="text-dark mb-3">Подтверждение пароля</h1>
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

        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                <span class="indicator-label">Подтвердить</span>
            </button>
        </div>
    </form>
@endsection
