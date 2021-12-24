@extends('layouts.app')

@section('toolbar-title', 'Акт за ' . $act->getDateFormatted())
@section('breadcrumbs', Breadcrumbs::render('acts.show', $act))

@section('content')
    <div class="post">
        <div class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Акт за {{ $act->getDateFormatted() }}</span>
                    </h3>

                    <div class="card-toolbar">
                        @can('edit acts')
                            <a href="{{ route('acts.edit', $act) }}" class="btn btn-sm btn-primary me-3">Изменить</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <div class="flex-grow-1">
                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Договор</label>
                                <div class="col-lg-10 fv-row">
                                    @if(auth()->user()->can('show contracts'))
                                        <a class="fw-bold fs-6" target="_blank" href="{{ route('contracts.show', $act->contract) }}">{{ $act->contract->getName() }}</a>
                                    @else
                                        <span class="fw-bold text-gray-800 fs-6">{{ $act->contract->getName() }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Компания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->company->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Объект</label>
                                <div class="col-lg-10 fv-row">
                                    @if(auth()->user()->can('show objects'))
                                        <a class="fw-bold fs-6" target="_blank" href="{{ route('objects.show', $act->object) }}" class="show-link">{{ $act->object->getName() }}</a>
                                    @else
                                        <span class="fw-bold text-gray-800 fs-6">{{ $act->object->getName() }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->getDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма выполнения</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->getAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма удержания аванса</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->getAvansAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма удержания депозита</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->getDepositAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма к оплате</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->getNeedPaidAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма оплаченных работ</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->getPaidAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма неоплаченных работ</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->getLeftPaidAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Описание</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->description }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Создал</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $act->createdBy->name }}</span>
                                    <span class="text-muted fw-bold text-muted fs-7">({{ $act->created_at->format('d/m/Y H:i') }})</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обновил</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($act->updatedBy)
                                        <span class="fw-bold text-gray-800 fs-6">{{ $act->updatedBy->name }}</span>
                                        <span class="text-muted fw-bold text-muted fs-7">({{ $act->updated_at->format('d/m/Y H:i') }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Статус</label>
                                <div class="col-lg-10 fv-row">
                                    @include('partials.status', ['status' => $act->getStatus()])
                                </div>
                            </div>

                            <h3 class="fw-bolder fs-3 my-9">Оплаты</h3>

                            @forelse($act->payments as $payment)
                                <p class="fw-bold text-gray-800 fs-6">{{ $payment->getDateFormatted() . ' - ' . $payment->getAmount() }}</p>
                            @empty
                                <span class="fw-bold text-gray-800 fs-6">Отсутствуют</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-flex flex-center py-3">
                        <a href="{{ route('acts.index') }}" class="btn btn-light">Отменить</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
