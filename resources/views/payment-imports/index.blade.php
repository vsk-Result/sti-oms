@extends('layouts.app')

@section('toolbar-title', 'Загрузки оплат')
@section('breadcrumbs', Breadcrumbs::render('payment_imports.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                            </svg>
                        </span>
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Поиск" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        @can('create payment-imports')
                            <button type="button" class="btn btn-light-primary"
                                    data-kt-menu-trigger="click"
                                    data-kt-menu-placement="bottom-start">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                    </svg>
                                </span>
                                Загрузить оплаты
                            </button>

                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="{{ route('payment_imports.types.statements.create') }}" class="menu-link px-3">
                                        Из выписки
                                    </a>
                                </div>

                                <div class="menu-item px-3">
                                    <a href="{{ route('payment_imports.types.crm_cost_closures.create') }}" class="menu-link px-3">
                                        Из кассы CRM
                                    </a>
                                </div>

                                <div class="menu-item px-3">
                                    <a href="{{ route('payment_imports.types.history.create') }}" class="menu-link px-3">
                                        Из истории оплат
                                    </a>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-120px">Тип</th>
                            <th class="min-w-120px">Дата</th>
                            <th class="min-w-120px">Компания</th>
                            <th class="min-w-120px">Банк</th>
                            <th class="min-w-80px">Оплат</th>
                            <th class="min-w-150px">Входящий остаток</th>
                            <th class="min-w-150px">Расход</th>
                            <th class="min-w-150px">Приход</th>
                            <th class="min-w-150px">Исходящий остаток</th>
                            <th class="min-w-120px">Загрузил</th>
                            <th class="min-w-120px">Статус</th>
                            <th class="min-w-100px text-end rounded-end pe-4">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($imports as $import)
                                <tr>
                                    <td class="text-dark fw-bolder">{{ $import->getType() }}</td>
                                    <td class="text-dark fw-bolder">
                                        @if (auth()->user()->can('show payment-imports'))
                                            <div class="menu-item px-3">
                                                <a href="{{ route('payment_imports.show', $import) }}">{{ $import->getDateFormatted() }}</a>
                                            </div>
                                        @else
                                            {{ $import->getDateFormatted() }}
                                        @endif
                                    </td>
                                    <td class="text-dark fw-bolder"><a href="#">{{ $import->company->short_name }}</a></td>
                                    <td class="text-dark fw-bolder"><a href="#">{{ $import->getBankName() }}</a></td>
                                    <td class="text-dark fw-bolder">{{ $import->payments_count }}</td>
                                    <td class="fw-bolder">
                                        @if($import->isStatement())
                                            <span class="{{ $import->incoming_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $import->getIncomingBalance() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-danger fw-bolder">{{ $import->getAmountPay() }}</td>
                                    <td class="text-success fw-bolder">{{ $import->getAmountReceive() }}</td>
                                    <td class="fw-bolder">
                                        @if($import->isStatement())
                                            <span class="{{ $import->outgoing_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $import->getOutgoingBalance() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-dark fw-bolder">{{ $import->createdBy->name }}</td>
                                    <td class="text-dark fw-bolder">@include('partials.status', ['status' => $import->getStatus()])</td>
                                    <td class="text-end text-dark fw-bolder">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                            @can('show payment-imports')
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('payment_imports.show', $import) }}" class="menu-link px-3">Посмотреть</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <form action="{{ route('payment_imports.exports.store', $import) }}" method="POST" class="hidden">
                                                        @csrf
                                                        <a
                                                            href="#"
                                                            class="menu-link px-3"
                                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                                        >
                                                            Экспорт
                                                        </a>
                                                    </form>
                                                </div>
                                            @endcan
                                            @can('edit payment-imports')
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('payment_imports.edit', $import) }}" class="menu-link px-3">Изменить</a>
                                                </div>

                                                <div class="menu-item px-3">
                                                    <form action="{{ route('payment_imports.destroy', $import) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                            href="#"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить выписку?')) {this.closest('form').submit();}"
                                                        >
                                                            Удалить
                                                        </a>
                                                    </form>
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Загрузки оплат отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
