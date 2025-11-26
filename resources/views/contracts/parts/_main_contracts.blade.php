@inject('currencyExchangeService', 'App\Services\CurrencyExchangeRateService')
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0 pt-6">
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterContractModal">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                        </svg>
                    </span>
                    Фильтр
                </button>

                @can('create contracts')
                    <a href="{{ route('contracts.create') }}{{ isset($object) ? ('?current_object_id=' . $object->id) : '' }}" class="btn btn-light-primary">
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                            </svg>
                        </span>
                        Новый договор
                    </a>
                @endcan

                @can('create acts')
                    <a href="{{ route('acts.create') }}{{ isset($object) ? ('?current_object_id=' . $object->id) : '' }}" class="btn btn-light-primary ms-4 me-3">
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                            </svg>
                        </span>
                        Новый акт
                    </a>
                @endcan

                <button type="button"
                        class="btn btn-bg-light btn-color-success"
                        data-kt-menu-trigger="click"
                        data-kt-menu-placement="bottom-start"
                >
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M11 11H13C13.6 11 14 11.4 14 12V21H10V12C10 11.4 10.4 11 11 11ZM16 3V21H20V3C20 2.4 19.6 2 19 2H17C16.4 2 16 2.4 16 3Z" fill="black"></path>
                                <path d="M21 20H8V16C8 15.4 7.6 15 7 15H5C4.4 15 4 15.4 4 16V20H3C2.4 20 2 20.4 2 21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21C22 20.4 21.6 20 21 20Z" fill="black"></path>
                            </svg>
                        </span>
                    Экспорт в Excel
                </button>

                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">
                    <div class="menu-item px-3">
                        <form action="{{ route('contracts.exports.store') . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
                            @csrf
                            <a
                                    href="javascript:void(0);"
                                    class="menu-link px-3"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                В одной таблице
                            </a>
                        </form>
                    </div>

                    <div class="menu-item px-3">
                        <form action="{{ route('contracts.exports.store') . (strpos(request()->fullUrl(), '?') !== false ? substr(request()->fullUrl(), strpos(request()->fullUrl(), '?')) : '') }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="split_contracts">
                            <a
                                    href="javascript:void(0);"
                                    class="menu-link px-3"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                В разных таблицах
                            </a>
                        </form>
                    </div>
                </div>

{{--                <button type="button"--}}
{{--                        class="btn btn-bg-light btn-color-info me-2"--}}
{{--                        data-kt-menu-trigger="click"--}}
{{--                        data-kt-menu-placement="bottom-start"--}}
{{--                >--}}
{{--                    <span class="svg-icon svg-icon-3">--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                            <path opacity="0.3" d="M11 11H13C13.6 11 14 11.4 14 12V21H10V12C10 11.4 10.4 11 11 11ZM16 3V21H20V3C20 2.4 19.6 2 19 2H17C16.4 2 16 2.4 16 3Z" fill="black"></path>--}}
{{--                            <path d="M21 20H8V16C8 15.4 7.6 15 7 15H5C4.4 15 4 15.4 4 16V20H3C2.4 20 2 20.4 2 21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21C22 20.4 21.6 20 21 20Z" fill="black"></path>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--                    Отчеты--}}
{{--                </button>--}}

{{--                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">--}}
{{--                    <div class="menu-item px-3">--}}
{{--                        <a href="#" class="menu-link px-3" data-bs-toggle="modal" data-bs-target="#lineChartPaymentActModal">--}}
{{--                            График выполнения--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
    <div class="card-body py-3 ps-3">
{{--        <div id="tr-1" class="table-responsive" style="height: 20px;overflow-y: hidden;"><div style="width: 100vw;height: 20px;"></div></div>--}}
        <div id="tr-2" class="table-responsive freeze-table">
            <table class="table table-hover align-middle table-row-dashed fs-7">
                <thead>
                <tr class="text-start text-muted fw-bolder fs-8 text-uppercase gs-0">
                    <th class="min-w-75px ps-4">Объект</th>
                    <th class="min-w-200px">Номер</th>
                    <th class="min-w-100px"></th>
                    <th class="min-w-150px">Сумма</th>
                    <th class="min-w-150px">Сумма аванса</th>
                    <th class="min-w-150px">Сумма получ. аванса</th>
                    <th class="min-w-150px">Сумма аванса к получению</th>
                    <th class="min-w-150px">Выполнено по актам</th>
                    <th class="min-w-150px">Аванс удержан по актам</th>
                    <th class="min-w-150px">Депозит удержан по актам</th>
                    <th class="min-w-150px">К оплате по актам</th>
                    <th class="min-w-150px">Оплачено по актам</th>
                    <th class="min-w-150px">Сумма неоплаченных работ по актам</th>
                    <th class="min-w-150px">Остаток неотработанного аванса</th>
                    <th class="min-w-125px rounded-end pe-4">Действия</th>
                </tr>
                <tr class="fw-bolder" style="background-color: #f7f7f7;">
                    <th colspan="3" class="ps-4" style="vertical-align: middle;">Итого</th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_received_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_received_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_left_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_left_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['acts_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['acts_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_avanses_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_avanses_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_deposites_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_deposites_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_paid_amount']['RUB'] + $total['avanses_acts_left_paid_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_paid_amount']['EUR'] + $total['avanses_acts_left_paid_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_paid_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_paid_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_left_paid_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_left_paid_amount']['EUR'], 'EUR') }}
                    </th>
                    <th>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_notwork_left_amount']['RUB'], 'RUB') }}
                        <br>
                        {{ \App\Models\CurrencyExchangeRate::format($total['avanses_notwork_left_amount']['EUR'], 'EUR') }}
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                @forelse($contracts as $contract)
                    @php
                        $childrenCount = $contract->children->count();
                        $showCurrencies = ['RUB' => 'RUB'];
                        $actsUrl = '';
                        foreach(array_merge([$contract->id], $contract->children->pluck('id')->toArray()) as $id) {
                            $actsUrl .= 'contract_id%5B%5D=' . $id . '&';
                        }
                    @endphp
                    <tr>
                        <td class="ps-4">
                            @if(auth()->user()->can('show objects'))
                                <a href="{{ route('objects.contracts.index', $contract->object) }}" class="show-link" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $contract->object->name }}">{{ $contract->object->code }}</a>
                            @else
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $contract->object->name }}">{{ $contract->object->code }}</span>
                            @endif
                        </td>
                        <td class="px-3">
                            @if(auth()->user()->can('edit contracts'))
                                <a href="{{ route('contracts.edit', $contract) }}" class="show-link">{{ $contract->getName() }}</a>
                            @else
                                {{ $contract->getName() }}
                            @endif
                        </td>
                        <td>
                            @foreach(['RUB', 'EUR'] as $currency)
                                @php
                                    $amount = $contract->getAmount($currency);
                                    $ch = $contract->children->where('currency', $currency)->count();
                                @endphp

                                @if ($ch > 0)
                                    <a
                                        href="#"
                                        class="btn btn-outline btn-outline-dashed btn-outline-default me-2 mb-2 show-subcontracts"
                                        data-show-subcontracts-url="{{ route('contracts.subcontracts.index', $contract) }}"
                                        data-currency="{{ $currency }}"
                                        style="padding: 0.4rem 0.5rem;font-size: 0.9rem;"
                                    >
                                        <i class="fas fa-arrow-down me-1"></i>
                                        {{ $currency }}
                                    </a>
                                    <br>

                                    @php
                                        $showCurrencies[$currency] = $currency;
                                    @endphp
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                {{ \App\Models\CurrencyExchangeRate::format($contract->getAmount($currency), $currency) }}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                {{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesAmount($currency), $currency) }}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                {{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesReceivedAmount($currency), $currency) }}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                {{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesLeftAmount($currency), $currency) }}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                <a href="{{ route('acts.index') }}?{{ $actsUrl }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsAmount($currency), $currency) }}</a>
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                <a href="{{ route('acts.index') }}?{{ $actsUrl }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsAvasesAmount($currency), $currency) }}</a>
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                <a href="{{ route('acts.index') }}?{{ $actsUrl }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsDepositesAmount($currency), $currency) }}</a>
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                <a href="{{ route('acts.index') }}?{{ $actsUrl }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsNeedPaidAmount($currency), $currency) }}</a>
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                <a href="{{ route('acts.index') }}?{{ $actsUrl }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsPaidAmount($currency), $currency) }}</a>
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                <a href="{{ route('acts.index') }}?{{ $actsUrl }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsLeftPaidAmount($currency), $currency) }}</a>
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($showCurrencies as $currency)
                                {{ \App\Models\CurrencyExchangeRate::format($contract->getNotworkLeftAmount($currency), $currency) }}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            <a href="#" class="btn-menu btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                <span class="svg-icon svg-icon-5 m-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                    </svg>
                                </span>
                            </a>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                @can('edit contracts')
                                    <div class="menu-item px-3">
                                        <a href="{{ route('contracts.edit', $contract) }}" class="menu-link px-3">Изменить</a>
                                    </div>

                                    <div class="menu-item px-3">
                                        <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                            <a
                                                href="#"
                                                class="menu-link px-3 text-danger"
                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить договор?')) {this.closest('form').submit();}"
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
                        <td colspan="14">
                            <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                Договора отсутствуют
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $contracts->links() }}
        </div>
    </div>
</div>
