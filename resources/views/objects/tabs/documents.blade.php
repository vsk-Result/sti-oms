@extends('objects.layouts.show')

@section('object-tab-title', 'Договора')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Договора</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>

                    <a href="#" class="btn btn-sm btn-light-primary align-self-center me-2">Добавить договор</a>
                </div>

                <div class="card-body p-9 pt-0 table-responsive">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Номер</th>
                            <th>Сумма</th>
                            <th>Сумма аванса</th>
                            <th>Сумма полученного аванса</th>
                            <th>Сумма аванса к получению</th>
                            <th>Выполнено по актам</th>
                            <th>Аванс удержан по актам</th>
                            <th>Депозит удержан по актам</th>
                            <th>К оплате по актам</th>
                            <th>Оплачено по актам</th>
                            <th>Сумма неоплаченных работ по актам</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            <tr>
                                <td class="min-w-250px">LG-05-21</td>
                                <td class="min-w-150px">886 522 662.42</td>
                                <td class="min-w-150px">312 331 156.50</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">190 843 125.01</td>
                                <td class="min-w-150px">10 284 829.07</td>
                                <td class="min-w-150px">140 308.45</td>
                                <td class="min-w-150px">44 088.92</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="min-w-250px">--- Доп. соглашение №1</td>
                                <td class="min-w-150px">886 522 662.42</td>
                                <td class="min-w-150px">312 331 156.50</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">190 843 125.01</td>
                                <td class="min-w-150px">10 284 829.07</td>
                                <td class="min-w-150px">140 308.45</td>
                                <td class="min-w-150px">44 088.92</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Изменить</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="min-w-250px">--- Доп. соглашение №2</td>
                                <td class="min-w-150px">886 522 662.42</td>
                                <td class="min-w-150px">312 331 156.50</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">190 843 125.01</td>
                                <td class="min-w-150px">10 284 829.07</td>
                                <td class="min-w-150px">140 308.45</td>
                                <td class="min-w-150px">44 088.92</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">10 100 431.70</td>
                                <td class="min-w-150px">0.00</td>
                                <td class="min-w-150px">
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
