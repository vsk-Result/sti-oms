@extends('layouts.app')

@section('toolbar-title', 'Оплаты за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')')
@section('breadcrumbs', Breadcrumbs::render('payment_imports.show', $import))

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">

            <div class="card mb-5 mb-xl-10">
                <div class="card-body pt-9 pb-0">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-4">
                                        <span class="text-gray-900 fs-2 fw-bolder me-1">{{ 'Оплаты за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    @can('edit payment-imports')
                                        <a href="{{ route('payment_imports.edit', $import) }}" class="btn btn-sm btn-primary me-3">Изменить</a>
                                    @endcan

                                    <form action="{{ route('payment_imports.exports.store', $import) }}" method="POST" class="hidden">
                                        @csrf
                                        <a
                                            href="#"
                                            class="btn btn-sm btn-primary me-3"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                        >
                                            Экспорт
                                        </a>
                                    </form>
                                </div>
                            </div>

                            @include('payment-imports.partials._general_info')
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Оплаты</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table-payments table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-120px">Дата</th>
                                <th class="min-w-120px">Объект</th>
                                <th class="min-w-70px">Кост код</th>
                                <th class="min-w-100px">Контрагент</th>
                                <th class="min-w-300px">Описание</th>
                                <th class="min-w-200px">Сумма</th>
                                <th class="min-w-150px">Категория</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($import->payments as $payment)
                                    <tr>
                                        <td class="ps-4">{{ $payment->getDateFormatted() }}</td>
                                        <td class="ps-4">
                                            @if ($payment->object)
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $payment->object->name }}">{{ $payment->getObject() }}</span>
                                            @else
                                                {{ $payment->getObject() }}
                                            @endif
                                        </td>
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
                                            <span class="{{ $payment->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ \App\Models\CurrencyExchangeRate::format($payment->amount, 'RUB') }}
                                            </span>

                                            @if ($payment->currency === 'RUB')
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    {{ \App\Models\CurrencyExchangeRate::format($payment->amount_without_nds, $payment->currency) }} без НДС
                                                </span>
                                            @else
                                                <span class="text-muted fw-bold text-muted d-block fs-7">
                                                    ({{ \App\Models\CurrencyExchangeRate::format($payment->currency_amount, $payment->currency) }})
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->category }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <p class="text-center text-dark fw-bolder d-block mb-1 fs-6">Оплаты отсутствуют</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        table tbody td {
            vertical-align: top;
        }
    </style>
@endpush
