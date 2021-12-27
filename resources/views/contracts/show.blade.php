@extends('layouts.app')

@section('toolbar-title', 'Договор ' . $contract->getName())
@section('breadcrumbs', Breadcrumbs::render('contracts.show', $contract))

@section('content')
    <div class="post">
        <div class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Договор {{ $contract->getName() }}</span>
                    </h3>

                    <div class="card-toolbar">
                        @can('edit contracts')
                            <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-primary me-3">Изменить</a>
                        @endcan
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                        <div class="flex-grow-1">
                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Компания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->company->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Объект</label>
                                <div class="col-lg-10 fv-row">
                                    @if(auth()->user()->can('show objects'))
                                        <a class="fw-bold fs-6" href="{{ route('objects.contracts.index', $contract->object) }}" class="show-link">{{ $contract->object->getName() }}</a>
                                    @else
                                        <span class="fw-bold text-gray-800 fs-6">{{ $contract->object->getName() }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Номер договора</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Тип договора</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getType() }}</span>
                                </div>
                            </div>

                            @if (! $contract->isMain())
                                <div class="row mb-7">
                                    <label class="col-lg-2 fw-bold text-muted">Основной договор</label>
                                    <div class="col-lg-10 fv-row">
                                        <a class="fw-bold fs-6" href="{{ route('contracts.show', $contract->parent) }}">{{ $contract->parent->getName() }}</a>
                                    </div>
                                </div>
                            @else
                                <div class="row mb-7">
                                    <label class="col-lg-2 fw-bold text-muted">Дочерние договора</label>
                                    <div class="col-lg-10 fv-row">
                                        @foreach($contract->children as $subContract)
                                            <a class="fw-bold fs-6 d-block mb-2" href="{{ route('contracts.show', $subContract) }}">{{ $subContract->getName() }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата начала</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getStartDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Дата окончания</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getEndDateFormatted() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Сумма</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getAmount() }}</span>
                                </div>
                            </div>

                            @if (! $contract->isMain())
                                <div class="row mb-7">
                                    <label class="col-lg-2 fw-bold text-muted">Тип суммы</label>
                                    <div class="col-lg-10 fv-row">
                                        <span class="fw-bold text-gray-800 fs-6">{{ $contract->getAmountType() }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Описание</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->description }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Создал</label>
                                <div class="col-lg-10 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->createdBy->name }}</span>
                                    <span class="text-muted fw-bold text-muted fs-7">({{ $contract->created_at->format('d/m/Y H:i') }})</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Обновил</label>
                                <div class="col-lg-10 fv-row">
                                    @if ($contract->updatedBy)
                                        <span class="fw-bold text-gray-800 fs-6">{{ $contract->updatedBy->name }}</span>
                                        <span class="text-muted fw-bold text-muted fs-7">({{ $contract->updated_at->format('d/m/Y H:i') }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Статус</label>
                                <div class="col-lg-10 fv-row">
                                    @include('partials.status', ['status' => $contract->getStatus()])
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-2 fw-bold text-muted">Файлы</label>
                                <div class="col-lg-10 fv-row">
                                    @foreach($contract->getMedia() as $media)
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="svg-icon svg-icon-2x svg-icon-primary me-4">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="black"></path>
                                                    <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black"></path>
                                                </svg>
                                            </span>
                                            <a target="_blank" href="{{ $media->getUrl() }}" class="text-gray-800 text-hover-primary">{{ $media->file_name . '      (' . $media->human_readable_size . ')' }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <h3 class="fw-bolder fs-3 my-9">Сводная информация</h3>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Сумма аванса</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getAvansesAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Сумма полученного аванса</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getAvansesReceivedAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Сумма аванса к получению</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getAvansesLeftAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Выполнено по актам</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getActsAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Аванс удержан по актам</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getActsAvasesAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Депозит удержан по актам</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getActsDepositesAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">К оплате по актам</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getActsNeedPaidAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Оплачено по актам</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getActsPaidAmount() }}</span>
                                </div>
                            </div>

                            <div class="row mb-7">
                                <label class="col-lg-3 fw-bold text-muted">Сумма неоплаченных работ по актам</label>
                                <div class="col-lg-9 fv-row">
                                    <span class="fw-bold text-gray-800 fs-6">{{ $contract->getActsLeftPaidAmount() }}</span>
                                </div>
                            </div>

                            <h3 class="fw-bolder fs-3 my-9">Авансы</h3>

                            @forelse($contract->avanses as $avans)
                                <p class="fw-bold text-gray-800 fs-6">{{ $avans->getAmount() }}</p>
                            @empty
                                <span class="fw-bold text-gray-800 fs-6">Отсутствуют</span>
                            @endforelse

                            <h3 class="fw-bolder fs-3 my-9">Полученные авансы</h3>

                            @forelse($contract->avansesReceived as $avans)
                                <p class="fw-bold text-gray-800 fs-6">{{ $avans->getDateFormatted() . ' - ' . $avans->getAmount() }}</p>
                            @empty
                                <span class="fw-bold text-gray-800 fs-6">Отсутствуют</span>
                            @endforelse

                            <h3 class="fw-bolder fs-3 my-9">Акты</h3>

                            <table class="table table-hover align-middle table-row-dashed fs-6">
                                <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-150px">Дата</th>
                                    <th class="min-w-150px">Выполнено</th>
                                    <th class="min-w-150px">Аванс удержан</th>
                                    <th class="min-w-150px">Депозит удержан</th>
                                    <th class="min-w-150px">К оплате</th>
                                    <th class="min-w-150px">Оплачено</th>
                                    <th class="min-w-150px">Сумма неоплаченных работ</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold">
                                    @forelse($contract->acts as $act)
                                        <tr>
                                            <td>
                                                @if(auth()->user()->can('show acts'))
                                                    <a href="{{ route('acts.show', $act) }}" class="show-link">{{ $act->getDateFormatted() }}</a>
                                                @else
                                                    {{ $act->getDateFormatted() }}
                                                @endif
                                            </td>
                                            <td>{{ $act->getAmount() }}</td>
                                            <td>{{ $act->getAvansAmount() }}</td>
                                            <td>{{ $act->getDepositAmount() }}</td>
                                            <td>{{ $act->getNeedPaidAmount() }}</td>
                                            <td>{{ $act->getPaidAmount() }}</td>
                                            <td>{{ $act->getLeftPaidAmount() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                                    Акты отсутствуют
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex flex-center py-3">
                        <a href="{{ url()->previous() }}" class="btn btn-light">Отменить</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
