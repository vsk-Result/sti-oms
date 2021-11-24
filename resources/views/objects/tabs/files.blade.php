@extends('objects.layouts.show')

@section('object-tab-title', 'Файлы')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Файлы</h3>
                    </div>

                    <a href="#" class="btn btn-sm btn-light-primary align-self-center">Добавить файлы</a>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Название</th>
                                <th>Размер</th>
                                <th>Загрузил</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody class="fw-bold text-gray-600">
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <img width="30" src="https://preview.keenthemes.com/metronic8/demo5/assets/media/svg/files/pdf.svg" alt="">
                                        </div>
                                        <a href="#" class="text-gray-800 text-hover-primary">Договор №П54О3-П32</a>
                                    </div>
                                </td>
                                <td>489 KB</td>
                                <td>
                                    Самсонов Владислав
                                    <p class="text-muted">23/11/2021 12:05</p>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-bg-light btn-color-muted btn-active-color-primary btn-sm px-4 me-2">Скачать</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <img width="30" src="https://preview.keenthemes.com/metronic8/demo5/assets/media/svg/files/doc.svg" alt="">
                                        </div>
                                        <a href="#" class="text-gray-800 text-hover-primary">Приложение №32 к договору №П54О3-П32</a>
                                    </div>
                                </td>
                                <td>3.2 МB</td>
                                <td>
                                    Самсонов Владислав
                                    <p class="text-muted">21/11/2021 09:40</p>
                                </td>
                                <td>
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
