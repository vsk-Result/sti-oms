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

    <div class="row g-5 g-xl-8 mb-6">
        @forelse($responsibleCashAccounts as $cashAccount)
            <div class="col-xl-4">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-body d-flex flex-column p-0 position-relative">

                        @if ($cashAccount->sharedUsers->count() > 0)
                            <span class=" position-absolute end-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Доступна другим пользователям">
                                <span class="btn btn-sm btn-icontranslate-middle" data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye fs-2"></i>
                                </span>
                            </span>
                        @endif

                        <div class="d-flex flex-stack flex-grow-1 card-p">
                            <div class="d-flex flex-column me-2">
                                <a href="{{ route('cash_accounts.show', $cashAccount) }}" class="text-gray-900 text-hover-primary fw-bold fs-3">{{ $cashAccount->name }}</a>

                                <span class="text-muted fw-semibold mt-1">{{ $cashAccount->responsible?->name }}</span>

                                <div class="d-flex gap-1 flex-row mt-4">
                                    @foreach($cashAccount->objects->sortBy('code') as $object)
                                        <a href="{{ route('objects.show', $object->id) }}"><span class="badge badge-light">{{ $object->code }}</span></a>
                                    @endforeach
                                </div>
                            </div>

                            <span class="symbol symbol-50px">
                                <span style="width: 100px" class="symbol-label fs-5 fw-bold {{ $cashAccount->getBalance() < 0 ? 'bg-light-danger text-danger' : 'bg-light-success text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($cashAccount->getBalance()) }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
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

        <div class="row g-5 g-xl-8">
            @foreach($sharedCashAccounts as $cashAccount)
                <div class="col-xl-4">
                    <div class="card card-xl-stretch mb-xl-8">
                        <div class="card-body d-flex flex-column p-0">
                            <div class="d-flex flex-stack flex-grow-1 card-p">
                                <div class="d-flex flex-column me-2">
                                    <a href="{{ route('cash_accounts.show', $cashAccount) }}" class="text-gray-900 text-hover-primary fw-bold fs-3">{{ $cashAccount->name }}</a>

                                    <span class="text-muted fw-semibold mt-1">{{ $cashAccount->responsible?->name }}</span>

                                    <div class="d-flex gap-1 flex-row mt-4">
                                        @foreach($cashAccount->objects->sortBy('code') as $object)
                                            <a href="{{ route('objects.show', $object->id) }}"><span class="badge badge-light">{{ $object->code }}</span></a>
                                        @endforeach
                                    </div>
                                </div>

                                <span class="symbol symbol-50px">
                                    <span style="width: 100px" class="symbol-label fs-5 fw-bold {{ $cashAccount->getBalance() < 0 ? 'bg-light-danger text-danger' : 'bg-light-success text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($cashAccount->getBalance()) }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(function() {});
    </script>
@endpush