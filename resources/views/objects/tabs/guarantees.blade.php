@extends('objects.layouts.show')

@section('object-tab-title', 'Гарантийные удержания')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Гарантийные удержания</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>

                    <a href="#" class="btn btn-sm btn-light-primary align-self-center">Добавить гарантийное удержание</a>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Договор</th>
                            <th>Заказчик</th>
                            <th class="min-w-125px">Сумма</th>
                            <th>Банковская гарантия</th>
                            <th>Статус</th>
                            <th>Условия выплаты</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td>ДОГОВОР ПОДРЯДА № 23/01/2021-ГП/Ves</td>
                                <td>На Тружениковом переулке</td>
                                <td>44 050 197.50</td>
                                <td>Есть</td>
                                <td>Контракт не закрыт  (срок до  30.04.21, но будет увеличение срока)</td>
                                <td>5% от стоимости выполненных и принятых работ - 1.25% в течение 10 р/д после акта приемки, 1.25% в течении 5 р/д после 1 года, 2.5% в течении 5 р/д после 2 лет (либо в течении 10 р/д после БГ)</td>
                                <td>
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                            <tr>
                                <td>ДОГОВОР ПОДРЯДА №ПД-00069546</td>
                                <td>ДС СТРОЙ</td>
                                <td>5 747 849</td>
                                <td>Нет</td>
                                <td>Ожидается в июле 2022 оставшвшиеся 5 %</td>
                                <td> 5% в течении 20 р/д после акта приемки, 5% в течении 20 р/б после гар.периода (24 месяца) , первые 5 % - 5 624 841 ,49  получены 16.10.20</td>
                                <td class="min-w-200px">
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Скачать</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
