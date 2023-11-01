@extends('layouts.app')

@section('toolbar-title',  'Долги')
@section('breadcrumbs', Breadcrumbs::render('debts.index'))

@section('content')
    @include('debts.modals.filter')
    <div class="post">
        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-danger">{{ \App\Models\CurrencyExchangeRate::format($total['amount_contractor']['RUB'], 'RUB') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма долга подрядчикам</div>
                        </div>
                    </div>

                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-danger">{{ \App\Models\CurrencyExchangeRate::format($total['amount_provider']['RUB'], 'RUB') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма долга поставщикам</div>
                        </div>
                    </div>

                    <div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                            <div class="d-flex flex-column align-items-left">
                                <div class="fs-5 fw-bolder text-danger">{{ \App\Models\CurrencyExchangeRate::format($total['amount_service']['RUB'], 'RUB') }}</div>
                            </div>
                            <div class="fw-bold fs-6 text-gray-400">Сумма долга за услуги</div>
                        </div>
                    </div>
                </div>

                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterDebtModal">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                                </svg>
                            </span>
                            Фильтр
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body py-3 px-0">
                <div class="table-responsive freeze-table">
                    <table class="table-payments table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="min-w-100px">Объект</th>
                            <th class="min-w-125px">Тип</th>
                            <th class="min-w-125px">Категория</th>
                            <th class="min-w-125px">Заказ сделал</th>
                            <th class="min-w-125px">Контрагент</th>
                            <th class="min-w-125px">Договор</th>
                            <th class="min-w-200px">Описание</th>
                            <th class="min-w-200px">Счет</th>
                            <th class="min-w-125px">Сумма счета</th>
                            <th class="min-w-125px">Сумма оплаты</th>
                            <th class="min-w-125px">Долг</th>
                            <th class="min-w-125px">ГУ</th>
                            <th class="min-w-125px">Авансы к оплате</th>
                            <th class="min-w-125px">Срок оплаты счета</th>
                            <th class="min-w-125px">Комментарий</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                        @forelse($debts as $debt)
                            <tr>
                                <td class="ps-4">
                                    @if ($debt->object)
                                        @if(auth()->user()->can('show objects'))
                                            <a href="{{ route('objects.show', $debt->object) }}" class="show-link" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $debt->object->name }}">{{ $debt->getObject() }}</a>
                                        @else
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $debt->object->name }}">{{ $debt->getObject() }}</span>
                                        @endif
                                    @else
                                        {{ $debt->object_id . ' не сущ.' }}
                                    @endif
                                </td>
                                <td>{{ $debt->getType() }}</td>
                                <td>{{ $debt->category }}</td>
                                <td>{{ $debt->order_author }}</td>
                                <td>{{ $debt->organization?->name }}</td>
                                <td>{{ $debt->contract }}</td>
                                <td>{{ $debt->description }}</td>
                                <td>{{ $debt->invoice_number }}</td>
                                <td>{{ \App\Models\CurrencyExchangeRate::format($debt->invoice_amount, 'RUB') }}</td>
                                <td>{{ \App\Models\CurrencyExchangeRate::format($debt->invoice_amount + $debt->amount, 'RUB') }}</td>
                                <td class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($debt->amount, 'RUB') }}</td>
                                <td class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($debt->guarantee, 'RUB') }}</td>
                                <td class="text-danger">{{ \App\Models\CurrencyExchangeRate::format($debt->avans, 'RUB') }}</td>
                                <td>{{ $debt->getDueDateFormatted() }}</td>
                                <td>{{ $debt->comment }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <p class="text-center text-dark fw-bolder d-block mb-1 fs-6">Долги отсутствуют</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $debts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);
        });
    </script>
@endpush
