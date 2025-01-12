@extends('layouts.app')

@section('title', 'Детализация кассы ' . $check->crmCost?->name . ' закрытого периода ' . $check->getFormattedPeriod())
@section('toolbar-title', 'Детализация кассы ' . $check->crmCost?->name . ' закрытого периода ' . $check->getFormattedPeriod())
@section('breadcrumbs', Breadcrumbs::render('crm_cash_check.show', $check))

@section('content')
    <div class="post">
        <a href="{{ route('crm_cash_check.index') }}" class="btn btn-light-primary mb-4">Вернуться</a>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-0 pt-6 pe-0">
                        <div class="card-title"></div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <form action="{{ route('crm_cash_check.exports.store', $check) }}" method="POST" class="hidden">
                                    @csrf
                                    <a
                                            href="javascript:void(0);"
                                            class="btn btn-light-primary"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                    >
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                </svg>
                            </span>
                                        Экспорт в Excel
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <table class="table table-hover align-middle table-row-dashed fs-6 mt-6">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-125px">Дата</th>
                                    <th class="min-w-100px">Объект</th>
                                    <th class="min-w-70px">Статья затрат</th>
                                    <th class="min-w-150px">Категория</th>
                                    <th class="min-w-150px">Сумма</th>
                                    <th class="min-w-125px">Контрагент</th>
                                    <th class="min-w-300px">Описание</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail['date'] }}
                                        </td>
                                        <td>
                                            @if (! empty($detail['object_name']))
                                                <span class="border-bottom-dashed" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $detail['object_name'] }}">{{ $detail['object_code'] }}</span>
                                            @else
                                                {{ $detail['object_code'] }}
                                            @endif
                                        </td>
                                        <td>{{ $detail['code'] }}</td>
                                        <td>{{ $detail['category'] }}</td>
                                        <td>
                                            <span class="{{ $detail['amount'] >= 0 ? 'text-success' : 'text-danger' }}">{{ \App\Models\CurrencyExchangeRate::format($detail['amount'], 'RUB') }}</span>
                                        </td>
                                        <td>
                                            {{ $detail['organization'] }}
                                        </td>
                                        <td>{{ $detail['description'] }}</td>
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
