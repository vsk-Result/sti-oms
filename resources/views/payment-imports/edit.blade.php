@extends('layouts.app')

@section('title', 'Изменение оплат за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')')
@section('toolbar-title', 'Изменение оплат за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')')
@section('breadcrumbs', Breadcrumbs::render('payment_imports.edit', $import))

@section('content')
    @include('sidebars.active_objects')
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
                                    @can('edit payment-imports')
                                        @if ($import->isStatement())
                                            <a href="{{ route('payment_imports.types.statements.edit', $import) }}" class="btn btn-sm btn-primary me-3">Изменить</a>
                                        @endif
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

                            @include('payment-imports.partials._general_info')
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
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase">
                                <th class="min-w-150px">Объект</th>
                                <th class="min-w-70px">Статья затрат</th>
                                <th class="min-w-100px">Контрагент</th>
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
