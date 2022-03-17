<div class="mt-6">
    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
        <div class="d-flex align-items-center">
            <div class="fs-4 fw-bolder">{{ $payments->total() }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Оплат</div>
    </div>

    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
        <div class="d-flex align-items-center">
            @php
                $balance = $totalInfo['amount_pay'] + $totalInfo['amount_receive'];
            @endphp
            <div class="fs-4 fw-bolder {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($balance, 2, '.', ' ') }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Баланс</div>
    </div>

    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
        <div class="d-flex align-items-center">
            <div class="fs-4 fw-bolder text-danger">{{ number_format($totalInfo['amount_pay'], 2, '.', ' ') }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Расходы</div>
    </div>

    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
        <div class="d-flex align-items-center">
            <div class="fs-4 fw-bolder text-success">{{ number_format($totalInfo['amount_receive'], 2, '.', ' ') }}</div>
        </div>
        <div class="fw-bold fs-6 text-gray-400">Приходы</div>
    </div>
</div>

<table class="table table-hover align-middle table-row-dashed fs-6">
    <thead>
    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
        <th data-sort-by="date" class="sortable-row min-w-125px">Дата</th>
        <th data-sort-by="company_id" class="sortable-row min-w-100px">Компания</th>
        <th data-sort-by="bank_id" class="sortable-row min-w-125px">Ответственный</th>
        <th data-sort-by="object_id" class="sortable-row min-w-100px">Объект</th>
        <th data-sort-by="code" class="sortable-row min-w-70px">Кост код</th>
        <th data-sort-by="organization_receiver_id" class="sortable-row min-w-125px">Организация</th>
        <th data-sort-by="description" class="sortable-row min-w-300px">Описание</th>
        <th data-sort-by="amount" class="sortable-row min-w-150px">Сумма</th>
        <th data-sort-by="category" class="sortable-row min-w-100px">Категория</th>
        <th class="min-w-125px text-end rounded-end pe-4">Действия</th>
    </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold">
    @forelse($payments as $payment)
        <tr>
            <td>
                @if (auth()->user()->can('edit payments'))
                    <a target="_blank" href="{{ route('payments.edit', $payment) }}" class="menu-link px-3">{{ $payment->getDateFormatted() }}</a>
                @else
                    {{ $payment->getDateFormatted() }}
                @endif
            </td>
            <td>{{ $payment->company->short_name }}</td>
            <td></td>
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
            <td class="text-end text-dark fw-bolder">
                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                    <span class="svg-icon svg-icon-5 m-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                            </svg>
                                                        </span>
                </a>
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-150px py-4" data-kt-menu="true">
                    @can('edit payments')
                        <div class="menu-item px-3">
                            <a target="_blank" href="{{ route('payments.edit', $payment) }}" class="menu-link px-3">Изменить</a>
                        </div>

                        <div class="menu-item px-3">
                            <form action="{{ route('payments.copy.store', $payment) }}" method="POST" class="hidden">
                                @csrf
                                <a
                                        href="javascript:void(0)"
                                        class="menu-link px-3"
                                        onclick="event.preventDefault(); if (confirm('Вы действительно хотите сделать копию оплаты?')) {this.closest('form').submit();}"
                                >
                                    Сделать копию
                                </a>
                            </form>
                        </div>

                        @if ($payment->audits->count() > 0)
                            <div class="menu-item px-3">
                                <a href="{{ route('payments.history.index') }}?payment_id={{ $payment->id }}" class="menu-link px-3">История</a>
                            </div>
                        @else
                            <div class="menu-item px-3" style="cursor:default !important;">
                                <span class="menu-link px-3 text-muted" style="cursor:default !important;">Истории нет</span>
                            </div>
                        @endif

                        <div class="menu-item px-3">
                            <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                                <a
                                        href="javascript:void(0)"
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
            <td colspan="9">
                <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                    Оплаты отсутствуют
                </p>
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

{{ $payments->links() }}