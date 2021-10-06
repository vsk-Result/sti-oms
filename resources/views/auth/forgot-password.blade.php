@extends('layouts.auth')

@section('content')
    <form class="form w-100" action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="text-center mb-10">
            <h1 class="text-dark mb-3">Забыли пароль ?</h1>
            <div class="text-gray-400 fw-bold fs-4">Введите ваш email для сброса пароля.</div>
        </div>

        <div class="fv-row mb-7">
            <label class="form-label fw-bolder text-dark fs-6">Email</label>
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

        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
            <button type="submit" class="btn btn-lg btn-primary fw-bolder me-4">
                <span class="indicator-label">Подтвердить</span>
            </button>
            <a href="{{ route('login') }}" class="btn btn-lg btn-light-primary fw-bolder">Отмена</a>
        </div>
    </form>
@endsection
