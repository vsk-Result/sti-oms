@extends('layouts.app')

@section('title', 'Кассы')
@section('toolbar-title', 'Кассы')
@section('breadcrumbs', Breadcrumbs::render('cash_accounts.index'))

@section('content')
    <h1 class="d-flex align-items-center justify-content-between my-1 mb-7">
        <span class="text-gray-900 fw-bold fs-2">
            Мои кассы
        </span>

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
    </h1>

    <div class="row g-5 g-xl-8">
        @forelse($responsibleCashAccounts as $cashAccount)
            <div class="col-xl-4">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-body d-flex flex-column p-0">
                        <div class="d-flex flex-stack flex-grow-1 card-p">
                            <div class="d-flex flex-column me-2">
                                <a href="{{ route('cash_accounts.show', $cashAccount) }}" class="text-gray-900 text-hover-primary fw-bold fs-3">{{ $cashAccount->name }}</a>

                                <span class="text-muted fw-semibold mt-1">{{ $cashAccount->responsible?->name }}</span>
                            </div>

                            <span class="symbol symbol-50px">
                                <span style="width: 100px" class="symbol-label fs-5 fw-bold bg-light-success text-success">{{ \App\Models\CurrencyExchangeRate::format($cashAccount->getBalance()) }}</span>
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
        <h1 class="d-flex align-items-center my-1 mb-7">
            <span class="text-gray-900 fw-bold fs-2">
                Доступные мне кассы
            </span>
        </h1>

        <div class="row g-5 g-xl-8">
            @foreach($sharedCashAccounts as $cashAccount)
                <div class="col-xl-3">
                    <div class="card bgi-no-repeat bgi-position-y-top bgi-position-x-end statistics-widget-1 card-xl-stretch mb-xl-8">
                        <div class="card-body">
                            <a href="#" class="card-title fw-bold text-muted text-hover-primary fs-4">{{ $cashAccount->name }}</a>

                            <div class="fw-bold text-{{ $cashAccount->getBalance() < 0 ? 'danger' : 'success' }} my-6">{{ \App\Models\CurrencyExchangeRate::format($cashAccount->getBalance()) }}</div>

                            {{--                        <p class="text-gray-900-75 fw-semibold fs-5 m-0">--}}
                            {{--                            Create a headline that is informative<br>--}}
                            {{--                            and will capture readers--}}
                            {{--                        </p>--}}
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