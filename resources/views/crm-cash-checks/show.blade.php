@extends('layouts.app')

@section('title', 'Детализация кассы ' . $check->crmCost->name . ' закрытого периода ' . $check->getFormattedPeriod())
@section('toolbar-title', 'Детализация кассы ' . $check->crmCost->name . ' закрытого периода ' . $check->getFormattedPeriod())
@section('breadcrumbs', Breadcrumbs::render('crm_cash_check.show', $check))

@section('content')
    <div class="post">
        <a href="{{ route('crm_cash_check.index') }}" class="btn btn-light-primary mb-4">Вернуться</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-3">
                        <table class="table table-hover align-middle table-row-dashed fs-6 mt-6">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Дата</th>
                                    <th class="min-w-100px">Объект</th>
                                    <th class="min-w-70px">Статья затрат</th>
                                    <th class="min-w-125px">Контрагент</th>
                                    <th class="min-w-300px">Описание</th>
                                    <th class="min-w-150px">Сумма</th>
                                    <th class="min-w-150px">Категория</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail['date'] }}
                                        </td>
                                        <td>
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $detail['object_name'] }}">{{ $detail['object_code'] }}</span>
                                        </td>
                                        <td>{{ $detail['code'] }}</td>
                                        <td>
                                            {{ $detail['organization'] }}
                                        </td>
                                        <td>{{ $detail['description'] }}</td>
                                        <td>
                                            <span class="{{ $detail['amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ $detail['amount'] }}</span>
                                        </td>
                                        <td>{{ $detail['category'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                                Оплаты отсутствуют
                                            </p>
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
