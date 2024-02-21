@php
    $total = $objectsInfo->total;
    $years = collect($objectsInfo->years)->toArray();
    $objects = $objectsInfo->objects;
    $summary = $objectsInfo->summary;
    $infos = [
            'Приходы' => 'receive',
            'Расходы' => 'pay',
            'Сальдо без общ. Расходов' => 'balance',
            'Общие расходы' => 'general_balance',
            'Сальдо c общ. Расходами' => 'balance_with_general_balance',
            'Долг подрядчикам' => 'contractor_debt',
            'Долг подрядчикам за ГУ' => 'contractor_debt_gu',
            'Долг поставщикам' => 'provider_debt',
            'Долг за услуги' => 'service_debt',
            'Долг на зарплаты ИТР' => 'itr_salary_debt',
            'Долг на зарплаты рабочим' => 'workers_salary_debt',
            'Долг Заказчика за выпол.работы' => 'dolgZakazchikovZaVipolnenieRaboti',
            'Долг Заказчика за ГУ (фактич.удерж.)' => 'dolgFactUderjannogoGU',
            'Текущий Баланс объекта' => 'objectBalance',
            'Сумма договоров с Заказчиком' => 'contractsTotalAmount',
            'Остаток неотработанного аванса' => 'ostatokNeotrabotannogoAvansa',
            'Остаток к получ. от заказчика (в т.ч. ГУ)' => 'ostatokPoDogovoruSZakazchikom',
            'Прогнозируемый Баланс объекта' => 'prognozBalance',
        ];
@endphp

<div class="card mt-5">
    <div class="card-header position-relative py-0 border-bottom-1 pe-0">
        <h3 class="card-title text-gray-800 fw-bold">Сводная по объектам</h3>
        <ul class="nav nav-tabs nav-stretch flex-nowrap text-nowrap fs-3">
            @foreach($years as $year => $objects)
                <li class="nav-item">
                    <a class="nav-link btn btn-color-gray-600 rounded-bottom-0 btn-active-light btn-active-color-primary {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#FO_year_{{ $year }}">{{ $year }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body pt-0 px-0">
        <div class="tab-content">
            @foreach($years as $year => $objects)
                <div class="tab-pane fade show {{ $loop->first ? 'active' : '' }}" id="FO_year_{{ $year }}" role="tabpanel">
                    <div class="table-responsive freeze-table">
                        <table class="objects-table table table-hover align-middle table-row-dashed fs-7 gy-5">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-200px br ps-2">Сводка</th>
                                <th class="min-w-200px hl">Итого</th>
                                @foreach($objects as $object)
                                    <th class="min-w-150px col-object {{ $loop->first ? 'bl' : '' }}">{{ $object->code . ' | '  . $object->name }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            @foreach($infos as $info => $field)
                                <tr>
                                    <td class="br ps-2">{{ $info }}</td>
                                    <td class="fw-bolder hl text-right">
                                        <span class="{{ $summary->{$year}->{$field} < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ \App\Models\CurrencyExchangeRate::format($summary->{$year}->{$field}, 'RUB') }}
                                        </span>
                                    </td>
                                    @foreach($objects as $object)
                                        <td class="text-right {{ $loop->first ? 'bl' : '' }} {{ $loop->last ? 'pe-4' : '' }}">
                                            <span class="{{ $total->{$year}->{$object->code}->{$field} < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ \App\Models\CurrencyExchangeRate::format($total->{$year}->{$object->code}->{$field}, 'RUB') }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
