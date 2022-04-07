@extends('layouts.app')

@section('toolbar-title', 'Компания ' . $company->name)
@section('breadcrumbs', Breadcrumbs::render('companies.show', $company))

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xxl-4 col-xxl-3">
            <div class="card card-xxl-stretch mb-5 mb-xxl-10">
                <div class="card-body">
                    <span class="fs-5 fw-bold text-gray-600 pb-6 d-block">Выберите дату</span>
                    <div class="d-flex align-self-center">
                        <div class="flex-grow-1 me-3">
                            <input
                                readonly
                                type="text"
                                class="form-control form-control-solid date-range-picker-single"
                                name="date"
                                value="{{ $date->format('Y-m-d') }}"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        <div class="col-lg-6 col-md-6 col-xxl-4 col-xxl-3">
            <div class="card h-100">
                <div class="card-body p-9">

                    <div class="fs-2hx fw-bolder">{{ \App\Models\CurrencyExchangeRate::format(array_sum($balances), 'RUB') }}</div>

                    <div class="d-flex align-items-center fs-4 fw-bold text-gray-400 mb-7">
                        <span class="d-flex">Баланс на {{ $date->format('d.m.Y') }}</span>
                    </div>

                    @foreach($balances as $bankName => $balance)
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="fw-bold">{{ $bankName }}</div>

                            <div class="d-flex fw-bolder">
                                {{ \App\Models\CurrencyExchangeRate::format($balance, 'RUB') }}
                            </div>
                        </div>

                        @if (! $loop->last)
                            <div class="separator separator-dashed"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-xxl-6 col-xxl-4">
            <div class="card">
                <div class="card-body p-9">

                    <div class="fs-2hx fw-bolder {{ $totalCreditAmount < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($totalCreditAmount, 'RUB') }}</div>

                    <div class="d-flex align-items-center fs-4 fw-bold text-gray-400 mb-7">
                        <span class="d-flex">Долг по кредитам на {{ $date->format('d.m.Y') }}</span>
                    </div>

                    @foreach($credits as $credit)
                        <div class="fs-6 d-flex justify-content-between my-4">
                            <div class="d-flex flex-column">
                                <div class="fw-bold">
                                    {{ $credit['bank'] }}
                                </div>
                                <p class="fs-7">
                                    <a
                                        target="_blank"
                                        class="text-muted"
                                        style="border-bottom: 1px dashed #ccc;"
                                        href="{{ route('payments.index') }}?bank_id%5B%5D={{ $credit['bank_id'] }}&description={{ str_replace(' ', '+', $credit['contract']) }}"
                                    >
                                        {{ $credit['contract'] }}
                                    </a>
                                </p>
                            </div>

                            <div>
                                <div class="d-flex">
                                    <div class="d-flex flex-column me-8">
                                        <span class="text-muted fs-8">Доступно</span>
                                        {{ \App\Models\CurrencyExchangeRate::format($credit['amount'] - $credit['received'], 'RUB') }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-8">В использовании</span>
                                        {{ \App\Models\CurrencyExchangeRate::format($credit['amount'], 'RUB') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (! $loop->last)
                            <div class="separator separator-dashed"></div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.applyBtn').on('click', function() {
                setTimeout(() => {
                    const url = new URL(document.location.href);
                    url.searchParams.set('balance_date', $('input[name=date]').val());
                    document.location = url.toString();
                }, 300);
            });
        });
    </script>
@endpush
