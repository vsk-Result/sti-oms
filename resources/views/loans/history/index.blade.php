@extends('layouts.app')

@section('toolbar-title', 'История оплат для ' . $loan->name . ' (' . $loan->getType() . ')')
@section('breadcrumbs', Breadcrumbs::render('loans.history.index', $loan))

@section('content')
    <div class="post">
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">

                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                        @can('create loans')
                            @if ($loan->is_auto_paid)
                                <a href="{{ route('loans.history.reload', $loan) }}" class="btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success">Обновить оплаты</a>
                            @endif
{{--                            @if ($loan->isCredit())--}}
{{--                                --}}
{{--                            @else--}}
{{--                                <a href="{{ route('loans.history.create', $loan) }}" class="btn btn-light-primary">--}}
{{--                                    <span class="svg-icon svg-icon-3">--}}
{{--                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>--}}
{{--                                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>--}}
{{--                                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>--}}
{{--                                        </svg>--}}
{{--                                    </span>--}}
{{--                                    Новая оплата--}}
{{--                                </a>--}}
{{--                            @endif--}}
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body pt-0 table-responsive freeze-table ps-0">
                <table class="table table-hover align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th class="min-w-125px ps-3">Дата оплаты</th>
                        <th class="min-w-125px">Сумма оплаты</th>
                        <th class="min-w-125px">Описание</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @forelse($historyPayments as $payment)
                            <tr>
                                <td class="ps-3">{{ $payment->getDateFormatted() }}</td>
                                <td>
                                    <span class="{{ $payment->amount >= 0 ? 'text-success' : 'text-danger' }}">{{ $payment->getAmount() }}</span>
                                        @if ($payment->currency !== 'RUB')
                                            <span class="text-muted fw-bold text-muted d-block fs-7" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="left" title="Курс: {{ $payment->currency_rate }}">
                                        ({{ \App\Models\CurrencyExchangeRate::format($payment->currency_amount, $payment->currency) }})
                                    </span>
                                    @else
                                        <span class="text-muted fw-bold text-muted d-block fs-7">{{ $payment->getAmountWithoutNDS() }} без НДС</span>
                                    @endif
                                </td>
                                <td>{!! nl2br($payment->description) !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
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
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });
    </script>
@endpush