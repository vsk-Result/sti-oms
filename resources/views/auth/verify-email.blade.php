@extends('layouts.auth')

@section('content')
    <div class="text-center mb-10">
        <h1 class="text-dark mb-3">Подтвердите Email</h1>
        <div class="text-gray-400 fw-bold fs-4">
            Спасибо, что зарегистрировались! Перед началом работы подтвердите свой адрес электронной почты, перейдя по ссылке, которую мы только что отправили вам по электронной почте. Если вы не получили электронное письмо, мы с радостью вышлем вам другое.
        </div>
    </div>

    <form class="form w-100" action="{{ route('verification.send') }}" method="POST">
        @csrf

        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                <span class="indicator-label">Выслать письмо с подтверждением снова</span>
            </button>
        </div>
    </form>

    <form class="form w-100" action="{{ route('logout') }}" method="POST">
        @csrf

        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-light-primary fw-bolder w-100 mb-5">
                <span class="indicator-label">Выйти</span>
            </button>
        </div>
    </form>
@endsection
