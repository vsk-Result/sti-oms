@extends('layouts.app')

@section('toolbar-title', 'План налогов к оплате')
@section('breadcrumbs', Breadcrumbs::render('tax_plan.index'))

@section('content')
    <div class="post" id="kt_post">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                        <a href="javascript::void(0);">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bolder text-danger">
                                    {{ \App\Models\CurrencyExchangeRate::format(\App\Models\TaxPlanItem::where('paid', false)->sum('amount'), 'RUB') }}
                                </div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Итого не оплачено</div>
                        </a>
                    </div>
                </div>

                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <form action="{{ route('tax_plan.exports.store') . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
                            @csrf
                            <a
                                    href="javascript:void(0);"
                                    class="btn btn-light-primary me-3"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                </svg>
                            </span>
                                Экспорт в Excel
                            </a>
                        </form>

                        @can('create tax-plan')
                            <a href="{{ route('tax_plan.create') }}" class="btn btn-light-primary me-3">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                    <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                    <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                </svg>
                            </span>
                                Новая запись
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Наименование</th>
                            <th class="min-w-125px">Сумма</th>
                            <th class="min-w-125px">Срок оплаты</th>
                            <th class="min-w-125px">Период</th>
                            <th class="min-w-125px">Платежка в 1С</th>
                            <th class="min-w-125px">Статус</th>
                            <th class="min-w-125px">Дата оплаты</th>
                            <th class="text-end min-w-100px">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ \App\Models\CurrencyExchangeRate::format($item->amount, 'RUB') }}</td>
                                <td>
                                    @if ($item->getDueDateStatus()['status'] === 'very-danger')
                                        <span class="border-bottom-dashed fw-boldest text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Просрочено на {{ $item->getDueDateStatus()['days'] }}">{{ $item->getDueDateFormatted() }}</span>
                                    @elseif($item->getDueDateStatus()['status'] === 'danger')
                                        <span class="border-bottom-dashed text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Осталось {{ $item->getDueDateStatus()['days'] }}">{{ $item->getDueDateFormatted() }}</span>
                                    @elseif($item->getDueDateStatus()['status'] === 'warning')
                                        <span class="border-bottom-dashed text-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Осталось {{ $item->getDueDateStatus()['days'] }}">{{ $item->getDueDateFormatted() }}</span>
                                    @else
                                        {{ $item->getDueDateFormatted() }}
                                    @endif
                                </td>
                                <td>{{ $item->period }}</td>
                                <td>{{ $item->in_one_c ? 'Да' : 'Нет' }}</td>
                                <td>
                                    @if ($item->paid)
                                        <span class="badge badge-success fw-bolder">Оплачено</span>
                                    @else
                                        <span class="badge badge-danger fw-bolder">Не оплачено</span>
                                    @endif
                                </td>
                                <td>{{ $item->getPaymentDateFormatted() }}</td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                        <span class="svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                            </svg>
                                        </span>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-150px py-4" data-kt-menu="true">
                                        @can('edit tax-plan')
                                            <div class="menu-item px-3">
                                                <a href="{{ route('tax_plan.edit', $item) }}" class="menu-link px-3">Изменить</a>
                                            </div>

                                            <div class="menu-item px-3">
                                                <form action="{{ route('tax_plan.destroy', $item) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a
                                                            href="{{ route('tax_plan.destroy', $item) }}"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить запись?')) {this.closest('form').submit();}"
                                                    >
                                                        Удалить
                                                    </a>
                                                </form>
                                            </div>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
