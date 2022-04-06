@extends('layouts.app')

@section('toolbar-title', 'Компания ' . $company->name)
@section('breadcrumbs', Breadcrumbs::render('companies.show', $company))

@section('content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-6 col-xxl-3">
            <div class="card h-100">
                <div class="card-body p-9">

                    <div class="fs-2hx fw-bolder">1 325 729 ₽</div>

                    <div class="d-flex align-items-center fs-4 fw-bold text-gray-400 mb-7">
                        <span class="d-flex">Баланс на {{ $date->format('d.m.Y') }}</span>
                        <a
                            href="#"
                            class="fs-4 ms-3 d-flex"
                            data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-start"
                            data-kt-menu-flip="top-start"
                        >
                            <i class="fa fa-calendar-alt"></i>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4"
                             data-kt-menu="true">
                            <div class="menu-item px-3">
                                <label class="form-label fw-bolder text-dark fs-6">Дата баланса</label>
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
