<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
        </div>

        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterGuaranteeModal">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                        </svg>
                    </span>
                    Фильтр
                </button>

                @can('create guarantees')
                    <a href="{{ route('guarantees.create') }}{{ isset($object) ? ('?current_object_id=' . $object->id) : '' }}" class="btn btn-light-primary me-3">
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                            </svg>
                        </span>
                        Новое ГУ
                    </a>
                @endcan
            </div>
        </div>
    </div>
    <div class="card-body py-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-row-dashed fs-7">
                <thead>
                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                    <th class="min-w-100px ps-4">Объект</th>
                    <th class="min-w-150px">Договор</th>
                    <th class="min-w-150px">Заказчик</th>
                    <th class="min-w-150px">Сумма ГУ (по договору)</th>
                    <th class="min-w-150px">Сумма ГУ (по факту)</th>
                    <th class="min-w-150px">БГ по условиям договора</th>
                    <th class="min-w-150px">Итоговый акт</th>
                    <th class="min-w-150px">Статус</th>
                    <th class="min-w-150px">Условия оплаты гар.удержания и комментарии</th>
                    <th class="min-w-150px">Действие</th>
                </tr>
                <tr class="fw-bolder" style="background-color: #f7f7f7;">
                    <th colspan="3" class="ps-4" style="vertical-align: middle;">Итого</th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['amount'], 'RUB') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['fact_amount'], 'RUB') }}
                    </th>
                    <th colspan="5"></th>
                </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                @forelse($guarantees as $guarantee)
                    <tr>
                        <td class="ps-4">
                            @if(auth()->user()->can('show objects'))
                                <a href="{{ route('objects.guarantees.index', $guarantee->object) }}" class="show-link">{{ $guarantee->object->code }}</a>
                            @else
                                {{ $guarantee->object->code }}
                            @endif
                        </td>
                        <td class="px-3">
                            @if(auth()->user()->can('index contracts'))
                                <a href="{{ route('contracts.index') }}?name={{ urlencode($guarantee->contract->parent ? $guarantee->contract->parent->name : $guarantee->contract->name) }}" class="show-link">{{ $guarantee->contract->getName() }}</a>
                            @else
                                {{ $guarantee->contract->getName() }}
                            @endif
                        </td>
                        <td>{{ $guarantee->customer->name }}</td>
                        <td>
                            @if(auth()->user()->can('edit guarantees'))
                                <a href="{{ route('guarantees.edit', $guarantee) }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($guarantee->amount, 'RUB') }}</a>
                            @else
                                {{ \App\Models\CurrencyExchangeRate::format($guarantee->amount, 'RUB') }}
                            @endif
                        </td>
                        <td>{{ \App\Models\CurrencyExchangeRate::format($guarantee->fact_amount, 'RUB') }}</td>
                        <td>{{ $guarantee->getBankGuaranteeState() }}</td>
                        <td>{{ $guarantee->getFinalActState() }}</td>
                        <td>{{ $guarantee->state }}</td>
                        <td>{{ $guarantee->conditions }}</td>
                        <td>
                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                <span class="svg-icon svg-icon-5 m-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                    </svg>
                                </span>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                @can('edit guarantees')
                                    <div class="menu-item px-3">
                                        <a target="_blank" href="{{ route('guarantees.edit', $guarantee) }}" class="menu-link px-3">Изменить</a>
                                    </div>

                                    <div class="menu-item px-3">
                                        <form action="{{ route('guarantees.destroy', $guarantee) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                            <a
                                                href="#"
                                                class="menu-link px-3 text-danger"
                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить гарантийное удержание?')) {this.closest('form').submit();}"
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
                        <td colspan="10">
                            <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                Гарантийные удержания отсутствуют
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $guarantees->links() }}
        </div>
    </div>
</div>
