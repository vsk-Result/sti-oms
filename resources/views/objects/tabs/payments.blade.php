@extends('objects.layouts.show')

@section('object-tab-title', 'Оплаты')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Оплаты</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>

                    <a href="#" class="btn btn-sm btn-light-primary align-self-center">Добавить оплату</a>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Источник</th>
                            <th>Дата</th>
                            <th>Компания</th>
                            <th>Банк</th>
                            <th>Кост код</th>
                            <th>Организация</th>
                            <th>Описание</th>
                            <th>Сумма</th>
                            <th>Категория</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td>Выписка</td>
                                <td>12/11/2021</td>
                                <td>СТИ</td>
                                <td>ПАО "ВТБ"</td>
                                <td>1.15</td>
                                <td>ООО "ЛАЙОН ГЕЙТ"</td>
                                <td>Оплата по дс №14 от 21.09.2021 к Договору №LG-05-21 от 30.03.2021. Счет №АВ/LG/ДС14/2 от 09.11.2021. Аванс Сумма 13499564-28 В т.ч. НДС (20%) 2249927-38</td>
                                <td class="min-w-200px">
                                    13 499 564.28
                                    <p class="text-muted">11 249 636.90 без НДС</p>
                                </td>
                                <td>MATERIAL</td>
                                <td>
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
