@extends('layouts.app')

@section('toolbar-title', 'Объект ' . $object->getName())
@section('breadcrumbs', Breadcrumbs::render('objects.show', $object))

@section('content')
    <div class="post" id="kt_post">
        <div class="card mb-6 mb-xl-9">
            <div class="card-body pt-9 pb-0">
                @include('objects.parts._object_general_info')

                <div class="separator"></div>
                <div class="d-flex overflow-auto h-55px">
                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-active-primary me-6 {{ request()->is('*/pivot') ? 'active' : '' }}" href="{{ route('objects.pivot.index', $object) }}">Сводная информация</a>--}}
{{--                        </li>--}}
                        @can('index payments')
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/payments') ? 'active' : '' }}" href="{{ route('objects.payments.index', $object) }}">Оплаты (безнал)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/cash-payments') ? 'active' : '' }}" href="{{ route('objects.cash_payments.index', $object) }}">Касса</a>
                            </li>
                        @endcan
                        @can('index contracts')
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/contracts') ? 'active' : '' }}" href="{{ route('objects.contracts.index', $object) }}">Договора</a>
                            </li>
                        @endcan
                        @can('index acts')
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/acts') ? 'active' : '' }}" href="{{ route('objects.acts.index', $object) }}">Акты</a>
                            </li>
                        @endcan
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-active-primary me-6 {{ request()->is('*/guarantees') ? 'active' : '' }}" href="{{ route('objects.guarantees.index', $object) }}">Гарантийные удержания</a>--}}
{{--                        </li>--}}
                        @can('index debts')
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/debts') ? 'active' : '' }}" href="{{ route('objects.debts.index', $object) }}">Долги</a>
                            </li>
                        @endcan
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-active-primary me-6 {{ request()->is('*/files') ? 'active' : '' }}" href="{{ route('objects.files.index', $object) }}">Файлы</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a class="nav-link text-active-primary me-6 {{ request()->is('*/activity') ? 'active' : '' }}" href="{{ route('objects.activity.index', $object) }}">Активность</a>--}}
{{--                        </li>--}}
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @yield('object-tab-content')
@endsection
