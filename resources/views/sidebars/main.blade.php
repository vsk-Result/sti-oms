<div id="kt_aside" class="aside" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '225px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_toggle" data-kt-sticky="true" data-kt-sticky-name="aside-sticky" data-kt-sticky-offset="{default: false, lg: '1px'}" data-kt-sticky-width="{lg: '225px'}" data-kt-sticky-left="auto" data-kt-sticky-top="94px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
    <div class="hover-scroll-overlay-y my-5 my-lg-5 w-100 ps-4 ps-lg-0 pe-4 me-1" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_header" data-kt-scroll-wrappers="#kt_aside" data-kt-scroll-offset="5px">
        <form id="kt_sidebar_search_form" style="display: none;"><div class="form-control"></div></form>

        <div class="aside-logo d-lg-none d-flex justify-content-center mb-3" id="kt_aside_logo">
            <a href="#">
                <img alt="Logo" src="{{ asset('images/logo.png') }}" class="h-20px logo">
            </a>
        </div>

        <div class="menu menu-column menu-active-bg menu-hover-bg menu-title-gray-700 fs-6 menu-rounded w-100" id="#kt_aside_menu" data-kt-menu="true">

            @if(auth()->user()->can('index payment-imports') || auth()->user()->can('index debt-imports'))
                <div class="menu-item pt-5">
                    <div class="menu-content pb-2">
                        <span class="menu-section text-muted text-uppercase fs-7 fw-bolder">Загрузки</span>
                    </div>
                </div>

                @can('index payment-imports')
                    <div class="menu-item">
                        <a href="{{ route('payment_imports.index') }}" class="menu-link {{ request()->is('payment-imports*') ? 'active' : '' }}">
                            <span class="menu-title">Загрузки оплат</span>
                        </a>
                    </div>
                @endcan

{{--                @can('index debt-imports')--}}
{{--                    <div class="menu-item">--}}
{{--                        <a href="{{ route('debt_imports.index') }}" class="menu-link {{ request()->is('debt-imports*') ? 'active' : '' }}">--}}
{{--                            <span class="menu-title">Загрузки долгов</span>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                @endcan--}}
            @endif

            <div class="menu-item pt-5">
                <div class="menu-content pb-2">
                    <span class="menu-section text-muted text-uppercase fs-7 fw-bolder">Основное</span>
                </div>
            </div>

            @can('index objects')
                <div class="menu-item">
                    <a href="{{ route('objects.index') }}" class="menu-link {{ request()->is('objects*') ? 'active' : '' }}">
                        <span class="menu-title">Объекты</span>
                    </a>
                </div>
            @endcan

            @can('index payments')
                <div class="menu-item">
                    <a href="{{ route('payments.index') }}" class="menu-link {{ request()->is('payments*') ? 'active' : '' }}">
                        <span class="menu-title">Оплаты</span>
                    </a>
                </div>
            @endcan

            @can('index contracts')
                <div class="menu-item">
                    <a href="{{ route('contracts.index') }}" class="menu-link {{ request()->is('contracts*') ? 'active' : '' }}">
                        <span class="menu-title">Договора</span>
                    </a>
                </div>
            @endcan

            @can('index acts')
                <div class="menu-item">
                    <a href="{{ route('acts.index') }}" class="menu-link {{ request()->is('acts*') ? 'active' : '' }}">
                        <span class="menu-title">Акты</span>
                    </a>
                </div>
            @endcan

            @can('index bank-guarantees')
                <div class="menu-item">
                    <a href="{{ route('bank_guarantees.index') }}" class="menu-link {{ request()->is('bank-guarantees*') ? 'active' : '' }}">
                        <span class="menu-title">Банковские гарантии</span>
                    </a>
                </div>
            @endcan

            @can('index companies')
                <div class="menu-item">
                    <a href="{{ route('companies.index') }}" class="menu-link {{ request()->is('companies*') ? 'active' : '' }}">
                        <span class="menu-title">Компании</span>
                    </a>
                </div>
            @endcan

            @can('index organizations')
                <div class="menu-item">
                    <a href="{{ route('organizations.index') }}" class="menu-link {{ request()->is('organizations*') ? 'active' : '' }}">
                        <span class="menu-title">Организации</span>
                    </a>
                </div>
            @endcan

{{--            <div class="menu-item pt-5">--}}
{{--                <div class="menu-content pb-2">--}}
{{--                    <span class="menu-section text-muted text-uppercase fs-7 fw-bolder">Отчеты</span>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="menu-item">--}}
{{--                <a href="{{ route('reports.itr_salary_object.create') }}" class="menu-link {{ request()->is('reports*') ? 'active' : '' }}">--}}
{{--                    <span class="menu-title">Отчет по расходам на ЗП ИТР по проектам</span>--}}
{{--                </a>--}}
{{--            </div>--}}

            @can('show admin-sidebar-menu')
                <div class="menu-item pt-5">
                    <div class="menu-content pb-2">
                        <span class="menu-section text-muted text-uppercase fs-7 fw-bolder">Администрирование</span>
                    </div>
                </div>

                @can('index admin-users')
                    <div class="menu-item">
                        <a href="{{ route('users.index') }}" class="menu-link {{ request()->is('users*') ? 'active' : '' }}">
                            <span class="menu-title">Пользователи</span>
                        </a>
                    </div>
                @endcan

                @can('index admin-roles')
                    <div class="menu-item">
                        <a href="{{ route('roles.index') }}" class="menu-link {{ request()->is('roles*') ? 'active' : '' }}">
                            <span class="menu-title">Роли доступа</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a href="{{ route('objects.users.index') }}" class="menu-link {{ request()->is('object-users*') ? 'active' : '' }}">
                            <span class="menu-title">Доступ к объектам</span>
                        </a>
                    </div>
                @endcan

                @can('index admin-log-manager')
                    <div class="menu-item">
                        <a href="{{ route('logs.index') }}" class="menu-link {{ request()->is('logs*') ? 'active' : '' }}">
                            <span class="menu-title">Менеджер логов</span>
                        </a>
                    </div>
                @endcan
            @endcan

            <div class="menu-item" style="display: none;">
                <div class="menu-link">
                    <span class="menu-title text-muted fs-7" id="kt_aside_categories_toggle" data-bs-toggle="collapse" data-bs-target="#kt_aside_categories_more">More Categories</span>
                </div>
            </div>
        </div>
    </div>
</div>
