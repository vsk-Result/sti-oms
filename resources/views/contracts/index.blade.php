@extends('layouts.app')

@section('toolbar-title', 'Договора')
@section('breadcrumbs', Breadcrumbs::render('contracts.index'))

@section('content')

    @include('contracts.modals.filter')

    <div class="post">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($total['amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($total['amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма договоров</div>
                        </div>

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_received_amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_received_amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма полученных авансов</div>
                        </div>
                    </div>

                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_left_amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_left_amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма аванса к получению</div>
                        </div>

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_paid_amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_paid_amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма оплаченных актов</div>
                        </div>
                    </div>

                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_left_paid_amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_left_paid_amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Долг подписанных актов</div>
                        </div>

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_deposites_amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_acts_deposites_amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Долг ГУ</div>
                        </div>
                    </div>
                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_non_closes_amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder text-success">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_non_closes_amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Не закрытая сумма договоров</div>
                        </div>

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_notwork_left_amount']['RUB'], 'RUB') }}</div>
                                <div class="fs-5 fw-bolder">{{ \App\Models\CurrencyExchangeRate::format($total['avanses_notwork_left_amount']['EUR'], 'EUR') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Остаток неотработ. аванса</div>
                        </div>
                    </div>
                </div>
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
                            <a href="{{ route('contracts.create') }}" class="btn btn-light-primary">
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
                            <a href="{{ route('acts.create') }}" class="btn btn-light-primary ms-4">
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
                    </div>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-100px ps-4">Объект</th>
                                <th class="min-w-250px">Номер</th>
                                <th class="min-w-100px"></th>
                                <th class="min-w-130px">Сумма</th>
                                <th class="min-w-130px">Сумма аванса</th>
                                <th class="min-w-130px">Сумма получ. аванса</th>
                                <th class="min-w-130px">Сумма аванса к получению</th>
                                <th class="min-w-130px">Выполнено по актам</th>
                                <th class="min-w-130px">Аванс удержан по актам</th>
                                <th class="min-w-130px">Депозит удержан по актам</th>
                                <th class="min-w-130px">К оплате по актам</th>
                                <th class="min-w-130px">Оплачено по актам</th>
                                <th class="min-w-130px">Сумма неоплаченных работ по актам</th>
                                <th class="min-w-150px rounded-end pe-4">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($contracts as $contract)
                                @php
                                    $childrenCount = $contract->children->count();
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        @if(auth()->user()->can('show objects'))
                                            <a href="{{ route('objects.contracts.index', $contract->object) }}" class="show-link">{{ $contract->object->code }}</a>
                                        @else
                                            {{ $contract->object->code }}
                                        @endif
                                    </td>
                                    <td class="px-3">
                                        {{ $contract->getName() }}
                                    </td>
                                    <td>
                                        @foreach($contract->getAmount(false) as $currency => $amount)
                                            @if ($childrenCount > 0 && $amount !== 0)
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
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getAvansesAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getAvansesReceivedAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getAvansesLeftAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getActsAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getActsAvasesAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getActsDepositesAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getActsNeedPaidAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getActsPaidAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($contract->getActsLeftPaidAmount(false) as $currency => $amount)
                                            {{ \App\Models\CurrencyExchangeRate::format($amount, $currency) }}
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
                                                    <a target="_blank" href="{{ route('contracts.edit', $contract) }}" class="menu-link px-3">Изменить</a>
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
                                    <td colspan="13">
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
    </div>
@endsection

@push('scripts')
    <script>
        $('.show-subcontracts').on('click', function(e) {
            e.preventDefault();

            if ($(this).hasClass('show-active')) {
                $('a').removeClass('show-active');
                $('tr').removeClass('contract-row-active');
                $('.subcontract-row').remove();
                return;
            }

            $('a').removeClass('show-active');
            $('tr').removeClass('contract-row-active');
            $('.subcontract-row').remove();

            const $tr = $(this).closest('tr');
            const url = $(this).data('show-subcontracts-url');
            const currency = $(this).data('currency');

            $(this).addClass('show-active');

            mainApp.sendAJAX(
                url,
                'GET',
                {
                    currency
                },
                (data) => {
                    $tr.after(data.contracts_view);
                    $tr.addClass('contract-row-active');
                },
                {},
                () => {
                    KTMenu.createInstances();
                },
            )
        });
    </script>
@endpush

@push('styles')
    <style>
        .subcontract-row {
            background-color: #fff1e1;
            border-top: 1px solid #ddc4c4 !important;
        }

        .contract-row-active, .contract-row-active:hover {
            background-color: bisque !important;
            --bs-table-accent-bg: bisque !important;
        }

        .show-subcontracts.show-active {
            color: #009ef7 !important;
            border-color: #009ef7 !important;
            background-color: #f1faff !important;
        }
        .subcontract-row:hover {
            --bs-table-accent-bg: #ffe4c4 !important;
        }
    </style>
@endpush

