@extends('objects.layouts.show')

@section('object-tab-title', 'План поступлений')

@section('object-tab-content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">План поступлений</div>
                </div>

                <div class="card-body py-3">
                    <div class="table-responsive freeze-table">
                        <table class="table table-bordered align-middle table-row-dashed fs-6 gy-5" data-update-url="{{ route('objects.receive_plan.store', $object) }}">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 gs-0 cell-center">
                                    <th class="min-w-400px ps-2">Основание</th>
                                    @foreach($periods as $period)
                                        <th class="min-w-250px">{{ $period['format'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="text-gray-600 fw-bold fs-7">
                                @foreach($reasons as $reasonId => $reason)
                                    <tr>
                                        <td class="ps-2">{{ $reason }}</td>

                                        @foreach($periods as $period)
                                            @php
                                                $amount = $plans->where('date', $period['start'])->where('reason_id', $reasonId)->first()->amount;
                                                if ($amount == 0) {
                                                    $amount = '';
                                                }
                                            @endphp

                                            <td>
                                                <input
                                                    type="text"
                                                    value="{{ $amount }}"
                                                    class="amount-mask form-control form-control-sm form-control-solid db-field"
                                                    autocomplete="off"
                                                    data-reason-id="{{ $reasonId }}"
                                                    data-date="{{ $period['start'] }}"
                                                />
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                <tr class="total-row">
                                    <td class="ps-2 fw-bolder">Итого</td>

                                    @foreach($periods as $period)
                                        <td class="text-right">{{ \App\Models\CurrencyExchangeRate::format($plans->where('date', $period['start'])->sum('amount'), 'RUB') }}</td>
                                    @endforeach
                                </tr>
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
        .table td, .table th, .table tbody tr:last-child td {
            border: 1px solid #c8c8c8 !important;
            color: #3f4254;
        }

        .text-right {
            text-align: right !important;
        }

        .cell-center {
            vertical-align: middle !important;
            text-align: center !important;
        }

        .total-row {
            background-color: #f7f7f7 !important;
            font-weight: bold !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(1);
        });

        $(document).on('focus', '.db-field', function() {
            $(this).data('initial-amount', $(this).val());
        });

        $(document).on('blur', '.db-field', function() {
            const $that = $(this);
            const reason_id = $that.data('reason-id');
            const date = $that.data('date');
            const amount = $that.val();
            const url = $('.table').data('update-url');

            if ($that.data('initial-amount') !== amount) {
                mainApp.sendAJAX(
                    url,
                    'POST',
                    {
                        reason_id,
                        date,
                        amount,
                    }
                );
            }
        });
    </script>
@endpush
