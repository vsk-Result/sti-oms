@extends('layouts.app')

@section('title', 'Проверка касс CRM')
@section('toolbar-title', 'Проверка касс CRM')
@section('breadcrumbs', Breadcrumbs::render('crm_cash_check.index'))

@section('content')
    @include('crm-cash-checks.modals.filter')

    <div class="post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1"></div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#filterCashChecksModal">
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

                    <div class="card-body py-3">
                        <table class="table align-middle table-row-dashed fs-6 mb-0">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Пользователь CRM</th>
                                    <th class="min-w-125px">Касса CRM</th>
                                    <th class="min-w-125px">Закрытый период</th>
                                    <th class="min-w-125px">Информация</th>
                                    <th class="min-w-125px">Итог проверки</th>
                                    <th class="min-w-125px">Статус</th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold">
                                @forelse($uncheckedChecksGroupedByPeriod as $period => $checks)

                                    <tr>
                                        <td colspan="13">
                                            <p class="text-left text-dark fw-bolder d-block m-0 fs-6 ps-3">
                                                <span class="badge badge-light-danger fs-7 fw-bolder">{{ App\Models\CashCheck\CashCheck::getFormattedPeriodGeneral($period) }}</span>
                                            </p>
                                        </td>
                                    </tr>

                                    @foreach($checks as $check)
                                        <tr>
                                            <td>
                                                {{ $check->crmUser?->name ?? 'Не определено (' . $check->crm_user_id . ')' }}
                                            </td>
                                            <td>
                                                <a target="_blank" href="http://crm.local/costs/{{ $check->crm_cost_id }}">{{ $check->crmCost?->name ?? 'Не определено (' . $check->crm_cost_id . ')' }}</a>
                                            </td>
                                            <td>
                                               {{ $check->getFormattedPeriod() }}
                                            </td>
                                            <td>
                                                <a href="{{ route('crm_cash_check.show', $check) }}">Посмотреть детали</a>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column fs-7 gap-3 mt-2">
                                                    @foreach($check->managers as $manager)
                                                        <div class="d-flex align-items-center flex-grow-1">
                                                            <div class="me-3">
                                                                @if ($manager->isChecked())
                                                                    <i class="text-success fas fa-check-circle"></i>
                                                                @elseif ($manager->isRevision())
                                                                    <i class="text-warning fas fa-clock"></i>
                                                                @else
                                                                    <i class="text-danger fas fa-times-circle"></i>
                                                                @endif
                                                            </div>

                                                            <div class="d-flex flex-column">
                                                                <a href="mailto:{{ $manager->manager->email }}">
                                                                    <span class="text-gray-900 text-hover-primary fs-7 fw-bold">
                                                                        @if ($manager->isUser())
                                                                            <span class="fw-boldest">Вы</span>
                                                                            @if (! $manager->check->isSended())
                                                                                @if ($manager->isChecked())
                                                                                    <a href="{{ route('crm_cash_check.manager.uncheck', $manager) }}" class="fw-boldest text-danger text-decoration-underline">Отменить проверку</a>
                                                                                @else
                                                                                    @if ($manager->isRevision())
                                                                                        <a href="{{ route('crm_cash_check.manager.check', $manager) }}" class="fw-boldest text-success text-decoration-underline">Подтвердить проверку</a>
                                                                                    @else
                                                                                        <a href="{{ route('crm_cash_check.manager.revision', $manager) }}" class="fw-boldest text-warning text-decoration-underline mb-2">Отправлена на доработку</a>
                                                                                        <a href="{{ route('crm_cash_check.manager.check', $manager) }}" class="fw-boldest text-success text-decoration-underline">Подтвердить проверку</a>
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        @else
                                                                            {{ $manager->manager->name }}
                                                                        @endif
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if (auth()->id() === 1)
    {{--                                                <a href="{{ route('crm_cash_check.manager.reset', $check) }}" class="fw-boldest text-warning text-decoration-underline">Обновить менеджеров</a>--}}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $check->getStatusColor() }} fw-bolder">{{ $check->getStatus() }}</span>
                                                <span class="badge badge-{{ $check->getEmailStatusColor() }} fw-bolder">{{ $check->getEmailStatus() }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr><td class="pt-5" colspan="6">Закрытых периодов касс к проверке не найдено</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{ $uncheckedChecks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
