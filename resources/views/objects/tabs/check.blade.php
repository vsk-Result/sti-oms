@extends('objects.layouts.show')

@section('object-tab-title', 'План поступлений')

@section('object-tab-content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body py-3 fs-6 pt-6">
                    <div class="mb-8">
                        <h3 class="mb-4">Проверка приходов от заказчика в оплатах и в документообороте</h3>

                        @if (! $checkInfo['customer_payments']['has_warning'])
                            <p>Статус: <span class="text-success">Приходы равны</span></p>
                        @else
                            <p>Статус: <span class="text-danger">Приходы неравны</span></p>
                            <p>Сумма отличается на <strong>{{ \App\Models\CurrencyExchangeRate::format(abs($checkInfo['customer_payments']['diff_amount']), 'RUB') }}</strong></p>
                            <ul>
                                <li class="py-1">Приходы от заказчика: <strong class="ms-2 text-success">{{ \App\Models\CurrencyExchangeRate::format($checkInfo['customer_payments']['payments_receive_amount'], 'RUB') }}</strong></li>
                                <li class="py-1">Получено авансов: <strong class="ms-2 text-success">{{ \App\Models\CurrencyExchangeRate::format($checkInfo['customer_payments']['avanses_receive_amount'], 'RUB') }}</strong></li>
                                <li class="py-1">Получено по актам: <strong class="ms-2 text-success">{{ \App\Models\CurrencyExchangeRate::format($checkInfo['customer_payments']['acts_receive_amount'], 'RUB') }}</strong></li>
                                <li class="py-1">Получено по ГУ: <strong class="ms-2 text-success">{{ \App\Models\CurrencyExchangeRate::format($checkInfo['customer_payments']['gu_receive_amount'], 'RUB') }}</strong></li>
                            </ul>
                        @endif
                    </div>

                    <div>
                        <h3 class="mb-4">Проверка загрузки файлов долгов</h3>

                        @if ($checkInfo['files_debts']['has_warning'])
                            <p>Статус: <span class="text-danger">Обнаружены проблемы с актуальностью файлов долгов</span></p>
                        @else
                            <p>Статус: <span class="text-success">Все файлы загружены вовремя</span></p>
                        @endif

                        <ul>
                            <li class="py-1">Файл долгов по подрядчикам: <strong class="ms-2">загружен <a href="{{ $checkInfo['files_debts']['object_contractors']['link'] }}" class="border-bottom-dashed text-default {{ $checkInfo['files_debts']['object_contractors']['has_warning'] ? 'text-danger' : 'text-success' }}">{{ $checkInfo['files_debts']['object_contractors']['uploaded_date'] }}</a></strong></li>
                            <li class="py-1">Файл долгов по поставщикам из 1С: <strong class="ms-2">загружен <a href="{{ $checkInfo['files_debts']['1c_providers']['link'] }}" class="border-bottom-dashed text-default {{ $checkInfo['files_debts']['1c_providers']['has_warning'] ? 'text-danger' : 'text-success' }}">{{ $checkInfo['files_debts']['1c_providers']['uploaded_date'] }}</a></strong></li>
                            <li class="py-1">Файл долгов по услугам из 1С: <strong class="ms-2">загружен <a href="{{ $checkInfo['files_debts']['1c_services']['link'] }}" class="border-bottom-dashed text-default {{ $checkInfo['files_debts']['1c_services']['has_warning'] ? 'text-danger' : 'text-success' }}">{{ $checkInfo['files_debts']['1c_services']['uploaded_date'] }}</a></strong></li>
                        </ul>
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

    </script>
@endpush
