<div class="d-flex mt-6 justify-content-between">
    <div class="d-flex">
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

    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
        <div>
            <a class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterPaymentModal">
                <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                    </svg>
                </span>
                Фильтр
            </a>
        </div>

        <form data-export-store-url="{{ route('payments.exports.store') }}" action="" method="POST" id="export-store-form" class="hidden">
            @csrf
            <button
                id="export-store-form-submit"
                type="button"
                class="btn btn-light-primary me-3"
            >
                <span class="svg-icon svg-icon-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                        <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                        <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                    </svg>
                </span>
                Экспорт в Excel
            </button>
        </form>
    </div>
</div>

<table class="table table-hover align-middle table-row-dashed fs-6 mt-6">
    <thead>
    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
        <th data-sort-by="date" class="sortable-row min-w-125px">Дата</th>
        <th data-sort-by="company_id" class="sortable-row min-w-100px">Компания</th>
        <th data-sort-by="bank_id" class="sortable-row min-w-125px">Источник</th>
        <th data-sort-by="object_id" class="sortable-row min-w-100px">Объект</th>
        <th data-sort-by="code" class="sortable-row min-w-70px">Кост код</th>
        <th data-sort-by="organization_receiver_id" class="sortable-row min-w-125px">Контрагент</th>
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
            <td>{{ $payment->import->description ?? '' }}</td>
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
