@extends('layouts.app')

@section('toolbar-title', 'Выписка за ' . $statement->getDateFormatted())
@section('breadcrumbs', Breadcrumbs::render('statements.show', $statement))

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
                                        <span class="text-gray-900 fs-2 fw-bolder me-1">Выписка за {{ $statement->getDateFormatted() }}</span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    @can('edit statements')
                                        <a href="{{ route('statements.edit', $statement) }}" class="btn btn-sm btn-primary me-3">Изменить</a>
                                    @endcan

                                    <form action="{{ route('statements.exports.store', $statement) }}" method="POST" class="hidden">
                                        @csrf
                                        <a
                                            href="{{ route('statements.exports.store', $statement) }}"
                                            class="btn btn-sm btn-primary me-3"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                        >
                                            Экспорт
                                        </a>
                                    </form>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->getDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Компания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->company->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Банк</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->getBankName() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Оплат</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->payments_count }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Входящий остаток</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="{{ $statement->incoming_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $statement->getIncomingBalance() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Расход</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-danger fw-bold fs-6">{{ $statement->getAmountPay() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Приход</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-success fw-bold fs-6">{{ $statement->getAmountReceive() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Исходящий остаток</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="{{ $statement->outgoing_balance >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-6">{{ $statement->getOutgoingBalance() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Загрузил</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $statement->createdBy->name }}</span>
                                    <span class="text-muted fw-bold text-muted fs-7">({{ $statement->created_at->format('d/m/Y H:i') }})</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обновил</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($statement->updatedBy)
                                        <span class="fw-bold text-gray-800 fs-6">{{ $statement->updatedBy->name }}</span>
                                        <span class="text-muted fw-bold text-muted fs-7">({{ $statement->updated_at->format('d/m/Y H:i') }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Статус</label>
                                <div class="col-lg-10 fv-row">
                                    @include('partials.status', ['status' => $statement->getStatus()])
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
                                <th class="min-w-180px">Статус</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($statement->payments as $payment)
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
                                        <td class="text-end">@include('partials.status', ['status' => $payment->getStatus()])</td>
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
