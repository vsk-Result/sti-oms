@extends('layouts.app')

@section('toolbar-title', 'Оплаты')
@section('breadcrumbs', Breadcrumbs::render('payments.index'))

@section('content')
    @include('payments.modals.filter')

    <div class="post">
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
                        <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#filterPaymentModal">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                                </svg>
                            </span>
                            Фильтр</button>
                    </div>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6" id="kt_table_users">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-120px">Источник</th>
                            <th class="min-w-120px">Дата</th>
                            <th class="min-w-120px">Компания</th>
                            <th class="min-w-120px">Банк</th>
                            <th class="min-w-120px">Объект</th>
                            <th class="min-w-70px">Кост код</th>
                            <th class="min-w-100px">Организация</th>
                            <th class="min-w-300px">Описание</th>
                            <th class="min-w-200px">Сумма</th>
                            <th class="min-w-150px">Категория</th>
                            <th class="min-w-120px">Создал</th>
                            <th class="min-w-120px">Статус</th>
                            <th class="min-w-100px text-end rounded-end pe-4">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->import ? $payment->import->getType() : 'Вручную' }}</td>
                                    <td>{{ $payment->getDateFormatted() }}</td>
                                    <td><a href="#">{{ $payment->company->short_name }}</a></td>
                                    <td><a href="#">{{ $payment->getBankName() }}</a></td>
                                    <td>{{ $payment->getObject() }}</td>
                                    <td>{{ $payment->code }}</td>
                                    <td>
                                        @if ($payment->amount < 0)
                                            {{ $payment->organizationReceiver->name }}
                                        @else
                                            {{ $payment->organizationSender->name }}
                                        @endif
                                    </td>
                                    <td>{{ $payment->description }}</td>
                                    <td>
                                        <span class="{{ $payment->amount >= 0 ? 'text-success' : 'text-danger' }}">{{ $payment->getAmount() }}</span>
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ $payment->getAmountWithoutNDS() }} без НДС</span>
                                    </td>
                                    <td>{{ $payment->category }}</td>
                                    <td>{{ $payment->createdBy->name }}</td>
                                    <td class="text-end">@include('partials.status', ['status' => $payment->getStatus()])</td>
                                    <td class="text-end text-dark fw-bolder">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                            <span class="svg-icon svg-icon-5 m-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                    </svg>
                                                </span>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                            @can('show payments')
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('payments.show', $payment) }}" class="menu-link px-3">Посмотреть</a>
                                                </div>
                                            @endcan
                                            @can('edit payments')
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('payments.edit', $payment) }}" class="menu-link px-3">Изменить</a>
                                                </div>

                                                <div class="menu-item px-3">
                                                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                            href="{{ route('payments.destroy', $payment) }}"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить оплату?')) {this.closest('form').submit();}"
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
                                    <td colspan="12">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Оплаты отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
