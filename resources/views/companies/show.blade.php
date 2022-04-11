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

            <div class="d-flex flex-wrap">
                <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                    <div class="d-flex align-items-center">
                        <div class="fs-2 fw-bolder {{ $totalDebtDTAmount < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($totalDebtDTAmount, 'RUB') }}</div>
                    </div>
                    <div class="fw-bold fs-6 text-gray-400">
                        Долг перед ДТ Термо
                    </div>
                    <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $totalDebtDTAmount }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                    </button>
                </div>

                <div class="pivot-box border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3 position-relative">
                    <div class="d-flex align-items-center">
                        <div class="fs-2 fw-bolder {{ $totalDebtPTIAmount < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($totalDebtPTIAmount, 'RUB') }}</div>
                    </div>
                    <div class="fw-bold fs-6 text-gray-400">
                        Долг перед ПТИ
                    </div>
                    <button class="btn btn-icon btn-sm btn-light btn-copy" data-clipboard-value="{{ $totalDebtPTIAmount }}">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="black"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="black"></path>
                                </svg>
                            </span>
                    </button>
                </div>
            </div>

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
                                    <div class="d-flex flex-column me-8">
                                        <span class="text-muted fs-8">В использовании</span>
                                        {{ \App\Models\CurrencyExchangeRate::format(abs($credit['sent']), 'RUB') }}
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-8">Всего</span>
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
