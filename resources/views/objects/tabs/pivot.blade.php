@extends('objects.layouts.show')

@section('object-tab-title', 'Сводная информация')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-4">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Долги</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Долг подрядчикам</td>
                                <td class="text-danger">13 245 620</td>
                            </tr>
                            <tr>
                                <td>Долг за материалы</td>
                                <td class="text-danger">10 773 800</td>
                            </tr>
                            <tr>
                                <td>Долг подписанных актов</td>
                                <td class="text-success">39 467 255</td>
                            </tr>
                            <tr>
                                <td>Долг гарантийного удержания</td>
                                <td class="text-success">44 900 039</td>
                            </tr>
                            <tr>
                                <td>Долг на зарплаты</td>
                                <td class="text-danger">14 211 590</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
