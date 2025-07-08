@extends('layouts.app')

@section('title', 'Проверка касс CRM')
@section('toolbar-title', 'Проверка касс CRM')
@section('breadcrumbs', Breadcrumbs::render('crm_cash_check.index'))

@section('content')
    <div class="post">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
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
