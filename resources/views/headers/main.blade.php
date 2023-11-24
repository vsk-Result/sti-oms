@inject('currencyExchangeService', 'App\Services\CurrencyExchangeRateService')

@include('headers.favourite-links.modals.create')

<div id="kt_header" class="header align-items-stretch">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 w-lg-225px me-5">
            <div class="btn btn-icon btn-active-icon-primary ms-n2 me-2 d-flex d-lg-none" id="kt_aside_toggle">
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
                    </svg>
                </span>
            </div>
            <a href="{{ route('home') }}">
                <img alt="Logo" src="{{ asset('images/logo.png') }}" class="d-none d-lg-inline h-45px" />
            </a>
        </div>
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">

            <div class="d-flex align-items-stretch" id="kt_header_nav">
                <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                    <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch" id="#kt_header_menu" data-kt-menu="true">
                        @include('headers.favourite-links.index')

                        <div class="menu-item menu-lg-down-accordion me-lg-1">
                            <span class="menu-link py-3">
                                <a class="menu-title" href="{{ route('helpdesk.tickets.index', ['status_id' => [\App\Models\Status::STATUS_ACTIVE]]) }}">Служба поддержки</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-stretch flex-shrink-0">
                <div class="d-flex align-items-stretch flex-shrink-0">
                    <div class="d-flex ms-1 ms-lg-2 flex-column justify-content-center me-8">
                        <div id="show-date-and-time" class="fs-4 fw-bold me-2"></div>
                    </div>

                    @php
                        $date = now();
                        $needFlag = true;
                        $USDExchangeRate = $currencyExchangeService->getExchangeRate($date->format('Y-m-d'), 'USD');
                        $EURExchangeRate = $currencyExchangeService->getExchangeRate($date->format('Y-m-d'), 'EUR');
                    @endphp
                    @include('partials.exchange_rate', compact('date', 'needFlag', 'USDExchangeRate', 'EURExchangeRate'))

                    @php
                        $date = now()->addDay();
                        $needFlag = false;
                        $USDExchangeRate = $currencyExchangeService->getExchangeRate($date->format('Y-m-d'), 'USD');
                        $EURExchangeRate = $currencyExchangeService->getExchangeRate($date->format('Y-m-d'), 'EUR');
                    @endphp
                    @include('partials.exchange_rate', compact('date', 'needFlag', 'USDExchangeRate', 'EURExchangeRate'))

                    <div class="d-flex align-items-center ms-lg-5" id="kt_header_user_menu_toggle">
                        <div class="btn btn-active-light d-flex align-items-center bg-hover-light py-2 px-2 px-md-3" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" data-kt-menu-flip="bottom">
                            <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2">
                                <span class="text-dark fs-base fw-bolder lh-1">{{ substr(auth()->user()->name, 0, strpos(auth()->user()->name, ' ')) }}</span>
                            </div>
                            <div class="symbol symbol-30px symbol-md-40px">
                                <img src="{{ auth()->user()->getPhoto() }}" alt="metronic" />
                            </div>
                        </div>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">
                                    <div class="symbol symbol-50px me-5">
                                        <img alt="Logo" src="{{ auth()->user()->getPhoto() }}" />
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="fw-bolder d-flex align-items-center fs-5">{{ auth()->user()->name }}</div>
                                        <a href="mailto:{{ auth()->user()->email }}" class="fw-bold text-muted text-hover-primary fs-7">{{ auth()->user()->email }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="separator my-2"></div>
                            <div class="menu-item px-5 my-1">
                                <a href="{{ route('users.edit', auth()->user()) }}" class="menu-link px-5">Настройки аккаунта</a>
                            </div>
                            <div class="menu-item px-5">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                    <a
                                        href="{{ route('logout') }}"
                                        class="menu-link px-5"
                                        onclick="event.preventDefault();this.closest('form').submit();"
                                    >
                                        Выйти
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
