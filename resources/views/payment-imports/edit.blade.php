@extends('layouts.app')

@section('toolbar-title', 'Изменение оплат за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')')
@section('breadcrumbs', Breadcrumbs::render('payment_imports.edit', $import))

@section('content')
    @include('sidebars.cost_codes')
    @include('payment-imports.modals.split_payment_modal')

    <div class="post d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="card mb-5 mb-xl-10">
                <div class="card-body pt-9 pb-0">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-4">
                                        <span class="text-gray-900 fs-2 fw-bolder me-1">Изменение оплат за {{ $import->getDateFormatted() }} ({{ $import->getType() }})</span>
                                    </div>
                                </div>
                                <div class="d-flex">
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
                                    <form action="{{ route('payment_imports.destroy', $import) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                        <a
                                            href="#"
                                            class="btn btn-sm btn-danger me-3"
                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить выписку?')) {this.closest('form').submit();}"
                                        >
                                            Удалить
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
                    <h3 class="card-title align-items-center justify-content-between flex-row w-100">
                        <span class="card-label fw-bolder fs-3 mb-1">Оплаты</span>

                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <span class="form-check-label fs-6 me-2">
                                Все
                            </span>
                            <input id="filter-payment" class="form-check-input h-20px w-40px" type="checkbox" value=""/>
                            <span class="form-check-label fs-6">
                                Без объекта
                            </span>
                        </label>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table data-payment-store-url="{{ route('payments.store') }}" class="table-payments table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Объект</th>
                                <th class="min-w-70px">Кост код</th>
                                <th class="min-w-100px">Организация</th>
                                <th class="min-w-400px">Описание</th>
                                <th class="min-w-150px">Сумма</th>
                                <th class="min-w-150px">Категория</th>
                                <th class="min-w-180px">Статус</th>
                                <th class="min-w-120px">Действие</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach($import->payments as $payment)
                                    @include('payment-imports.partials._edit_payment_table_row')
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/payment-imports/edit.js') }}"></script>
@endpush
