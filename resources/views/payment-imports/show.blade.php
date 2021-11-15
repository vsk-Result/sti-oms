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

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Тип</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $import->getType() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $import->getDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Компания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $import->company->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Банк</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $import->getBankName() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Оплат</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $import->payments_count }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Входящий остаток</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="{{ $import->incoming_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $import->getIncomingBalance() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Расход</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-danger fw-bold fs-6">{{ $import->getAmountPay() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Приход</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-success fw-bold fs-6">{{ $import->getAmountReceive() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Исходящий остаток</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="{{ $import->outgoing_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $import->getOutgoingBalance() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Описание</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $import->description }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Загрузил</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $import->createdBy->name }}</span>
                                    <span class="text-muted fw-bold text-muted fs-7">({{ $import->created_at->format('d/m/Y H:i') }})</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обновил</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($import->updatedBy)
                                        <span class="fw-bold text-gray-800 fs-6">{{ $import->updatedBy->name }}</span>
                                        <span class="text-muted fw-bold text-muted fs-7">({{ $import->updated_at->format('d/m/Y H:i') }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Статус</label>
                                <div class="col-lg-10 fv-row">
                                    @include('partials.status', ['status' => $import->getStatus()])
                                </div>
                            </div>
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
                                <th class="min-w-120px">Объект</th>
                                <th class="min-w-70px">Кост код</th>
                                <th class="min-w-100px">Организация</th>
                                <th class="min-w-300px">Описание</th>
                                <th class="min-w-200px">Сумма</th>
                                <th class="min-w-150px">Категория</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($import->payments as $payment)
                                    <tr>
                                        <td class="ps-4">{{ $payment->getObject() }}</td>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
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
