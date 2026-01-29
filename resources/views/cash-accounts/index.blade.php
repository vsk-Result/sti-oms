@inject('cashAccountNotificationService', 'App\Services\CashAccount\NotificationService')

@extends('layouts.app')

@section('title', 'Кассы')
@section('toolbar-title', 'Кассы')
@section('breadcrumbs', Breadcrumbs::render('cash_accounts.index'))

@section('content')
    <h1 class="d-flex align-items-center justify-content-between my-1 mb-7">
        <span class="text-gray-900 fw-bold fs-2">
            Мои кассы
        </span>

        @if (auth()->user()->hasRole('super-admin'))
            <a href="{{ route('cash_accounts.create') }}" class="btn btn-light-primary">
                <span class="svg-icon svg-icon-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                    </svg>
                </span>
                Новая касса
            </a>
        @endif
    </h1>

    <div class="row g-5 g-xl-8 mb-7">
        @forelse($responsibleCashAccounts as $cashAccount)
            @include('cash-accounts.parts._cash_account', $cashAccount)
        @empty
            <p class="text-gray-900-75 fw-semibold fs-5 mt-8">
                Вы еще не создали ни одну кассу
            </p>
        @endforelse
    </div>

    @if ($sharedCashAccounts->count() > 0)
        <h1 class="d-flex align-items-center mb-7">
            <span class="text-gray-900 fw-bold fs-2">
                Доступные мне кассы
            </span>
        </h1>

        <div class="row g-5 g-xl-8 mb-7">
            @foreach($sharedCashAccounts as $cashAccount)
                @include('cash-accounts.parts._cash_account', $cashAccount)
            @endforeach
        </div>
    @endif

    @if ($archivedCashAccounts->count() > 0)
        <h1 class="d-flex align-items-center mb-7">
            <span class="text-gray-900 fw-bold fs-2">
                Архивные кассы
            </span>
        </h1>

        <div class="row g-5 g-xl-8 mb-7">
            @foreach($archivedCashAccounts as $cashAccount)
                @include('cash-accounts.parts._cash_account', $cashAccount)
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(function() {});
    </script>
@endpush