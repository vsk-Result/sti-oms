@extends('layouts.app')

@section('toolbar-title', 'Займы / Кредиты')
@section('breadcrumbs', Breadcrumbs::render('loans.index'))

@section('content')
    @include('loans.modals.filter')
    <div class="post">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder {{ $total['amount_loan'] < 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($total['amount_loan'], 'RUB') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма долга по займам</div>
                        </div>
                    </div>

                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder {{ $total['amount_credit'] < 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($total['amount_credit'], 'RUB') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма долга по кредитам</div>
                        </div>
                    </div>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterLoanModal">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                            </svg>
                        </span>
                        Фильтр
                    </button>

                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        @can('create loans')
                            <a href="{{ route('loans.create') }}" class="btn btn-light-primary">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                    </svg>
                                </span>
                                Новый займ / кредит
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body pt-0 table-responsive freeze-table ps-0">
                <table class="table table-hover align-middle table-row-dashed fs-7 gy-5" id="kt_table_users">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="min-w-75px ps-3">Тип</th>
                        <th class="min-w-75px">Компания</th>
                        <th class="min-w-100px">Банк</th>
                        <th class="min-w-125px">Контрагент</th>
                        <th class="min-w-150px">Номер</th>
                        <th class="min-w-125px">Дата зачисления</th>
                        <th class="min-w-125px">Дата окончания</th>
                        <th class="min-w-125px">Сумма займа/кредита</th>
                        <th class="min-w-125px">Сумма оплачено</th>
                        <th class="min-w-125px">Сумма долга</th>
                        <th class="min-w-100px">Проценты</th>
                        <th class="min-w-400px">Описание</th>
                        <th class="min-w-125px">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                    @forelse($loans as $loan)
                        <tr>
                            <td class="ps-3">{{ $loan->getType() }}</td>
                            <td>{!! $loan->company?->getShortNameColored() !!}</td>
                            <td>{{ $loan->getBankName() }}</td>
                            <td>{{ $loan->organization?->name }}</td>
                            <td>
                                @if(auth()->user()->can('edit loans'))
                                    <a href="{{ route('loans.edit', $loan) }}" class="show-link">{{ $loan->name }}</a>
                                @else
                                    {{ $loan->name }}
                                @endif
                            </td>
                            <td>{{ $loan->getStartDateFormatted() }}</td>
                            <td>{{ $loan->getEndDateFormatted() }}</td>
                            <td class="fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($loan->total_amount, 'RUB') }}</td>
                            <td class="{{ $loan->getPaidAmount() < 0 ? 'text-danger' : '' }}">{{ \App\Models\CurrencyExchangeRate::format($loan->getPaidAmount(), 'RUB') }}</td>
                            <td class="{{ $loan->amount < 0 ? 'text-danger' : 'text-success' }}">{{ \App\Models\CurrencyExchangeRate::format($loan->amount, 'RUB') }}</td>
                            <td>{{ \App\Models\CurrencyExchangeRate::format($loan->percent, '', 2) }}</td>
                            <td>{!! nl2br($loan->description) !!}</td>
                            <td class="text-end">
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                        </svg>
                                    </span>
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a target="_blank" href="{{ route('loans.history.index', $loan) }}" class="menu-link px-3">Оплаты</a>
                                    </div>
                                    @can('edit loans')
                                        <div class="menu-item px-3">
                                            <a href="{{ route('loans.edit', $loan) }}" class="menu-link px-3">Изменить</a>
                                        </div>

                                        <div class="menu-item px-3">
                                            <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                                <a
                                                    href="#"
                                                    class="menu-link px-3 text-danger"
                                                    onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить займ/кредит?')) {this.closest('form').submit();}"
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
                                    Займы/кредиты отсутствуют
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $loans->links() }}
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