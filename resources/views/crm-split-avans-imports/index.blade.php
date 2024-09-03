@extends('layouts.app')

@section('title', 'Статус переноса оплат на карты из CRM')
@section('toolbar-title', 'Статус переноса оплат на карты из CRM')
@section('breadcrumbs', Breadcrumbs::render('crm.avanses.imports.split.index'))

@section('content')
    <div class="post">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header">
                        <div class="card-title">
                            Статус переноса оплат на карты из CRM
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive freeze-table">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                                <thead>
                                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                        <th class="min-w-125px">Дата</th>
                                        <th class="min-w-125px">Компания</th>
                                        <th class="min-w-125px">Кол-во записей</th>
                                        <th class="min-w-125px">Сумма</th>
                                        <th class="min-w-125px">Тип</th>
                                        <th class="min-w-125px">Описание</th>
                                        <th class="min-w-125px">Оплаты разбиты в OMS</th>
                                        <th class="min-w-125px">Действие</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold">
                                    @foreach($imports as $import)
                                        <tr>
                                            <td>{{ $import->getDate() }}</td>
                                            <td>{{ $import->company->short_name }}</td>
                                            <td>{{ $import->items->count() }}</td>
                                            <td>{{ \App\Models\CurrencyExchangeRate::format($import->getItemsSum(), 'RUB') }}</td>
                                            <td>{{ $import->type }}</td>
                                            <td>{{ $import->description }}</td>
                                            <td align="center"><span class="badge fw-bolder badge-{{ $import->is_split ? 'success' : 'danger' }}">{{ $import->is_split ? 'Да' : 'Нет' }}</span></td>
                                            <td>
                                                <a href="http://crm.local/avanses/import/{{ $import->id }}/show">Просмотр</a>
                                                | <a class="text-green" href="http://crm.local/avanses/import/{{ $import->id }}/excel">Excel</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{ $imports->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            mainApp.initFreezeTable(2);
        });
    </script>
@endpush
