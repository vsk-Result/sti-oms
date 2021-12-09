@extends('layouts.app')

@section('toolbar-title', 'Долги за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')')
@section('breadcrumbs', Breadcrumbs::render('debt_imports.show', $import))

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
                                        <span class="text-gray-900 fs-2 fw-bolder me-1">{{ 'Долги за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')' }}</span>
                                    </div>
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
                                <label class="col-lg-2 fw-bold text-muted">Долг подрядчикам</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-danger fw-bold fs-6">{{ number_format($import->debts->where('type_id', 0)->sum('amount'), 2, '.', ' ') }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Долг поставщикам</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="text-danger fw-bold fs-6">{{ number_format($import->debts->where('type_id', 1)->sum('amount'), 2, '.', ' ') }}</span>
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
                        <span class="card-label fw-bolder fs-3 mb-1">Долги</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table-payments table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-120px">Объект</th>
                                <th class="min-w-100px">Организация</th>
                                <th class="min-w-200px">Сумма</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($import->debts as $debt)
                                    <tr>
                                        <td class="ps-4">{{ $debt->getObject() }}</td>
                                        <td>{{ $debt->organization->name }}</td>
                                        <td>
                                            <span class="text-danger">{{ $debt->getAmount() }}</span>
                                            <span class="text-muted fw-bold text-muted d-block fs-7">{{ $debt->getAmountWithoutNDS() }} без НДС</span>
                                        </td>
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
