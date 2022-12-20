@extends('layouts.app')

@section('toolbar-title', 'История оплат для ' . $loan->name . ' (' . $loan->getType() . ')')
@section('breadcrumbs', Breadcrumbs::render('loans.history.index', $loan))

@section('content')
    <div class="post">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">

                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        @can('create loans')
                            @if ($loan->isCredit())
                                <a href="{{ route('loans.history.reload', $loan) }}" class="btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success">Обновить оплаты</a>
                            @else
                                <a href="{{ route('loans.history.create', $loan) }}" class="btn btn-light-primary">
                                    <span class="svg-icon svg-icon-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                        </svg>
                                    </span>
                                    Новая оплата
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body pt-0 table-responsive freeze-table ps-0">
                <table class="table table-hover align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="min-w-125px ps-3">Дата оплаты</th>
                        <th class="min-w-125px">Планируемая дата возврата</th>
                        <th class="min-w-125px">Дата возврата</th>
                        <th class="min-w-125px">Сумма</th>
                        <th class="min-w-125px">Процент</th>
                        <th class="min-w-125px">Итоговая сумма</th>
                        <th class="min-w-125px">Описание</th>
                        <th class="min-w-125px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse($historyPayments as $payment)
                            <tr>
                                <td class="ps-3">{{ $payment->getDateFormatted() }}</td>
                                <td>{{ $payment->getPlannedRefundDateFormatted() }}</td>
                                <td>{{ $payment->getRefundDateFormatted() }}</td>
                                <td class="{{ $payment->amount >= 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($payment->amount, 'RUB') }}</td>
                                <td class="{{ $payment->percent >= 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($payment->percent, 'RUB') }}</td>
                                <td class="fw-boldest {{ ($payment->amount + $payment->percent) >= 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($payment->amount + $payment->percent, 'RUB') }}</td>
                                <td>{!! nl2br($payment->description) !!}</td>
                                <td>
                                    <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                        <span class="svg-icon svg-icon-5 m-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                            </svg>
                                        </span>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                        @can('edit loans')
                                            <div class="menu-item px-3">
                                                <a href="{{ route('loans.history.edit', [$loan, $payment]) }}" class="menu-link px-3">Изменить</a>
                                            </div>

                                            <div class="menu-item px-3">
                                                <form action="{{ route('loans.history.destroy', [$loan, $payment]) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a
                                                            href="#"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить оплату займа/кредита?')) {this.closest('form').submit();}"
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
                                <td colspan="8">
                                    <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                        Оплаты отсутствуют
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });
    </script>
@endpush