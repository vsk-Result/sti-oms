@extends('objects.layouts.show')

@section('object-tab-title', 'Долги')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-6">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Долг подрядчикам</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Организация</th>
                            <th>Сумма</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($object->getContractorDebts() as $debt)
                                <tr>
                                    <td>{{ $debt->organization->name }}</td>
                                    <td class="text-danger">{{ number_format($debt->amount, 2, '.', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Долги отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-flush h-lg-100">
                <div class="card-header mt-6">
                    <div class="card-title flex-column">
                        <h3 class="fw-bolder mb-1">Долг поставщикам</h3>
                        <div class="fs-6 fw-bold text-gray-400">На {{ now()->format('d.m.Y') }}</div>
                    </div>
                </div>

                <div class="card-body p-9 pt-0">
                    <table class="table table-hover align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th>Организация</th>
                            <th>Сумма</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @forelse($object->getProviderDebts() as $debt)
                                <tr>
                                    <td>{{ $debt->organization->name }}</td>
                                    <td class="text-danger">{{ number_format($debt->amount, 2, '.', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Долги отсутствуют
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
