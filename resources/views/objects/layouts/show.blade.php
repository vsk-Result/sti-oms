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
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/payments') || request()->is('*/reports/payments-receive') ? 'active' : '' }}" href="{{ route('objects.payments.index', $object) }}?object_id%5B%5D={{ $object->id }}">Оплаты</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/writeoffs') ? 'active' : '' }}" href="{{ route('objects.writeoffs.index', $object) }}?object_id%5B%5D={{ $object->id }}">Списания</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/cash-payments') ? 'active' : '' }}" href="{{ route('objects.cash_payments.index', $object) }}">Касса</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/contracts') ? 'active' : '' }}" href="{{ route('objects.contracts.index', $object) }}?object_id%5B%5D={{ $object->id }}">Договора</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/acts') || request()->is('*/reports/act-category') ? 'active' : '' }}" href="{{ route('objects.acts.index', $object) }}?object_id%5B%5D={{ $object->id }}">Акты</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/guarantees') ? 'active' : '' }}" href="{{ route('objects.guarantees.index', $object) }}">Гарантийные удержания</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/debts') ? 'active' : '' }}" href="{{ route('objects.debts.index', $object) }}">Долги</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/bank-guarantees') ? 'active' : '' }}" href="{{ route('objects.bank_guarantees.index', $object) }}?object_id%5B%5D={{ $object->id }}">Банковские гарантии и депозиты</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/receive-plan') ? 'active' : '' }}" href="{{ route('objects.receive_plan.index', $object) }}?object_id%5B%5D={{ $object->id }}">План поступлений</a>
                            </li>
                            <li class="nav-item d-flex flex-row align-items-center">
                                @inject('objectCheckService', 'App\Services\ObjectCheckService')

                                @if($objectCheckService->getCheckStatusInfo($object)['has_warning'])
                                    <i class="fa fa-info-circle text-danger"></i>
                                @endif
                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/check') ? 'active' : '' }}" href="{{ route('objects.check.index', $object) }}?object_id%5B%5D={{ $object->id }}">Проверка</a>
                            </li>
{{--                            <li class="nav-item">--}}
{{--                                <a class="nav-link text-active-primary me-6 {{ request()->is('*/deposits') ? 'active' : '' }}" href="{{ route('objects.deposits.index', $object) }}?object_id%5B%5D={{ $object->id }}">Депозиты без БГ</a>--}}
{{--                            </li>--}}
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


