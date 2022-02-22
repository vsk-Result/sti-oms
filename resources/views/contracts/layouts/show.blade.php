@extends('layouts.app')

@section('toolbar-title', 'Договор ' . $contract->getName())
@section('breadcrumbs', Breadcrumbs::render('contracts.show', $contract))

@section('content')
    <div class="post">
        <div class="card mb-6 mb-xl-9">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-1">
                                    <a href="{{ route('contracts.show', $contract) }}" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-3">{{ 'Договор ' . $contract->getName() }}</a>
                                    <span class="badge badge-light-success me-auto">{{ $contract->getType() }}</span>
                                </div>

                                <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                                    <div class="d-flex align-items-center text-gray-400  me-5 mb-2">
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="black"></path>
                                                <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        @if(auth()->user()->can('show objects'))
                                            <a href="{{ route('objects.contracts.index', $contract->object) }}">{{ $contract->object->getName() }}</a>
                                        @else
                                            {{ $contract->object->getName() }}
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center text-gray-400 me-5 mb-2">
                                        {{ $contract->getStartDateFormatted() . ' - ' . $contract->getEndDateFormatted() }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex mb-4">
                                @can('edit contracts')
                                    <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-light btn-active-light-primary btn-sm me-3">Изменить</a>
                                @endcan
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-start">
                            <div class="d-flex flex-wrap">

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAmount(true, 'EUR'), 'EUR')  }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Сумма договора</div>
                                </div>

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesAmount(true, 'EUR'), 'EUR')  }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Сумма авансов</div>
                                </div>

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesReceivedAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesReceivedAmount(true, 'EUR'), 'EUR')  }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Сумма полученных авансов</div>
                                </div>

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesLeftAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesLeftAmount(true, 'EUR'), 'EUR')  }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Сумма аванса к получению</div>
                                </div>

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsPaidAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsPaidAmount(true, 'EUR'), 'EUR')  }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Сумма оплаченных актов</div>
                                </div>

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsLeftPaidAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsLeftPaidAmount(true, 'EUR'), 'EUR')  }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Долг подписанных актов</div>
                                </div>

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsDepositesAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsDepositesAmount(true, 'EUR'), 'EUR')  }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Долг ГУ</div>
                                </div>

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex flex-column align-items-left">
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAmount(true, 'RUB') - $contract->getAvansesReceivedAmount(true, 'RUB') - $contract->getActsPaidAmount(true, 'RUB') - $contract->getActsDepositesAmount(true, 'RUB') - $contract->getActsLeftPaidAmount(true, 'RUB'), 'RUB') }}</div>
                                        <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($contract->getAmount(true, 'EUR') - $contract->getAvansesReceivedAmount(true, 'EUR') - $contract->getActsPaidAmount(true, 'EUR') - $contract->getActsDepositesAmount(true, 'EUR') - $contract->getActsLeftPaidAmount(true, 'EUR'), 'EUR') }}</div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Не закрытая сумма договора</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="separator"></div>
                <div class="d-flex overflow-auto h-55px">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-active-primary me-6 {{ request()->fullUrl() === route('contracts.show', $contract) ? 'active' : '' }}" href="{{ route('contracts.show', $contract) }}">Информация</a>--}}
{{--                        </li>--}}
                        <li class="nav-item">
                            <a class="nav-link text-active-primary me-6 {{ request()->is('*/acts') ? 'active' : '' }}" href="{{ route('contracts.acts.index', $contract) }}">Акты</a>
                        </li>
                        @if ($contract->isMain())
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/subcontracts') ? 'active' : '' }}" href="{{ route('contracts.subcontracts.index', $contract) }}">Дочерние договора</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @yield('contract-tab-content')
@endsection
