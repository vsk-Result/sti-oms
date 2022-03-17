@extends('objects.layouts.show')

@section('object-tab-title', 'Долги')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-6">
            @if ($object->code === '288')

                @php
                    $contractorDebts = $object->getContractorDebts();

                    $oneAmount = 0;
                    $twoFourAmount = 0;
                    foreach($contractorDebts as $organizationName => $debt) {
                        $one = $debt[1] ?? 0;
                        $two = $debt[2] ?? 0;
                        $four = $debt[4] ?? 0;
                        $seven = $debt[7] ?? 0;
                        $total = $one + $two + $four;
                        $oneTotal = 0;
                        $twoFourTotal = 0;
                        if ($total !== 0) {
                            $oneTotal = $one / $total;
                            $twoFourTotal = ($two + $four) / $total;
                        } elseif ($total === 0 && $seven !== 0) {
                            $twoFourTotal = 1;
                        }

                        $oneAmount += $oneTotal * $seven + $one;
                        $twoFourAmount += $twoFourTotal * $seven + $two + $four;
                    }
                @endphp

                <div class="card card-flush mb-3">
                    <div class="card-header mt-6">
                        <div class="card-title flex-column">
                            <h3 class="fw-bolder mb-1">Долг подрядчикам .1</h3>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($oneAmount, 2, ',', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
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
                                @forelse($contractorDebts as $organizationName => $debt)
                                    @php
                                        $one = $debt[1] ?? 0;
                                        $two = $debt[2] ?? 0;
                                        $four = $debt[4] ?? 0;
                                        $seven = $debt[7] ?? 0;
                                        $total = $one + $two + $four;
                                        $oneTotal = 0;
                                        $twoFourTotal = 0;
                                        if ($total !== 0) {
                                            $oneTotal = $one / $total;
                                            $twoFourTotal = ($two + $four) / $total;
                                        } elseif ($total === 0 && $seven !== 0) {
                                            $twoFourTotal = 1;
                                        }
                                    @endphp

                                    @if (($oneTotal * $seven + $one) < 0)
                                        <tr>
                                            <td>{{ $organizationName }}</td>
                                            <td class="text-danger">{{ number_format($oneTotal * $seven + $one, 2, ',', ' ') }}</td>
                                        </tr>
                                    @endif
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

                <div class="card card-flush">
                    <div class="card-header mt-6">
                        <div class="card-title flex-column">
                            <h3 class="fw-bolder mb-1">Долг подрядчикам .2 + .4</h3>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($twoFourAmount, 2, ',', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
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
                                @forelse($contractorDebts as $organizationName => $debt)
                                    @php
                                        $one = $debt[1] ?? 0;
                                        $two = $debt[2] ?? 0;
                                        $four = $debt[4] ?? 0;
                                        $seven = $debt[7] ?? 0;
                                        $total = $one + $two + $four;
                                        $oneTotal = 0;
                                        $twoFourTotal = 0;
                                        if ($total !== 0) {
                                            $oneTotal = $one / $total;
                                            $twoFourTotal = ($two + $four) / $total;
                                        } elseif ($total === 0 && $seven !== 0) {
                                            $twoFourTotal = 1;
                                        }
                                    @endphp

                                    @if (($twoFourTotal * $seven + $two + $four) < 0)
                                        <tr>
                                            <td>{{ $organizationName }}</td>
                                            <td class="text-danger">{{ number_format($twoFourTotal * $seven + $two + $four, 2, ',', ' ') }}</td>
                                        </tr>
                                    @endif
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
            @else
                <div class="card card-flush h-lg-100">
                    <div class="card-header mt-6">
                        <div class="card-title flex-column">
                            <h3 class="fw-bolder mb-1">Долг подрядчикам</h3>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($object->getContractorDebtsAmount(), 2, ',', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
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
                                @forelse($object->getContractorDebts() as $organizationName => $amount)
                                    <tr>
                                        <td>{{ $organizationName }}</td>
                                        <td class="text-danger">{{ number_format($amount, 2, ',', ' ') }}</td>
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
            @endif
        </div>

        <div class="col-lg-6">
            @if ($object->code === '288')

                @php
                    $providerDebts = $object->getProviderDebts();

                    $oneAmount = 0;
                    $twoFourAmount = 0;
                    foreach($providerDebts as $organizationName => $debt) {
                        $one = $debt[1] ?? 0;
                        $two = $debt[2] ?? 0;
                        $four = $debt[4] ?? 0;
                        $seven = $debt[7] ?? 0;
                        $total = $one + $two + $four;
                        $oneTotal = 0;
                        $twoFourTotal = 0;
                        if ($total !== 0) {
                            $oneTotal = $one / $total;
                            $twoFourTotal = ($two + $four) / $total;
                        } elseif ($total === 0 && $seven !== 0) {
                            $twoFourTotal = 1;
                        }

                        $oneAmount += $oneTotal * $seven + $one;
                        $twoFourAmount += $twoFourTotal * $seven + $two + $four;
                    }
                @endphp

                <div class="card card-flush mb-3">
                    <div class="card-header mt-6">
                        <div class="card-title flex-column">
                            <h3 class="fw-bolder mb-1">Долг поставщикам .1</h3>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($oneAmount, 2, ',', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
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
                            @forelse($providerDebts as $organizationName => $debt)
                                @php
                                    $one = $debt[1] ?? 0;
                                    $two = $debt[2] ?? 0;
                                    $four = $debt[4] ?? 0;
                                    $seven = $debt[7] ?? 0;
                                    $total = $one + $two + $four;
                                    $oneTotal = 0;
                                    $twoFourTotal = 0;
                                    if ($total !== 0) {
                                        $oneTotal = $one / $total;
                                        $twoFourTotal = ($two + $four) / $total;
                                    } elseif ($total === 0 && $seven !== 0) {
                                        $twoFourTotal = 1;
                                    }
                                @endphp

                                @if (($oneTotal * $seven + $one) < 0)
                                    <tr>
                                        <td>{{ $organizationName }}</td>
                                        <td class="text-danger">{{ number_format($oneTotal * $seven + $one, 2, ',', ' ') }}</td>
                                    </tr>
                                @endif
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

                <div class="card card-flush">
                    <div class="card-header mt-6">
                        <div class="card-title flex-column">
                            <h3 class="fw-bolder mb-1">Долг поставщикам .2 + .4</h3>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($twoFourAmount, 2, ',', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
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
                            @forelse($providerDebts as $organizationName => $debt)
                                @php
                                    $one = $debt[1] ?? 0;
                                    $two = $debt[2] ?? 0;
                                    $four = $debt[4] ?? 0;
                                    $seven = $debt[7] ?? 0;
                                    $total = $one + $two + $four;
                                    $oneTotal = 0;
                                    $twoFourTotal = 0;
                                    if ($total !== 0) {
                                        $oneTotal = $one / $total;
                                        $twoFourTotal = ($two + $four) / $total;
                                    } elseif ($total === 0 && $seven !== 0) {
                                        $twoFourTotal = 1;
                                    }
                                @endphp

                                @if (($twoFourTotal * $seven + $two + $four) < 0)
                                    <tr>
                                        <td>{{ $organizationName }}</td>
                                        <td class="text-danger">{{ number_format($twoFourTotal * $seven + $two + $four, 2, ',', ' ') }}</td>
                                    </tr>
                                @endif
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
            @else
                <div class="card card-flush h-lg-100">
                    <div class="card-header mt-6">
                        <div class="card-title flex-column">
                            <h3 class="fw-bolder mb-1">Долг поставщикам</h3>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">{{ number_format($object->getProviderDebtsAmount(), 2, ',', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
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
                            @forelse($object->getProviderDebts() as $organizationName => $amount)
                                <tr>
                                    <td>{{ $organizationName }}</td>
                                    <td class="text-danger">{{ number_format($amount, 2, ',', ' ') }}</td>
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
            @endif
        </div>
    </div>
@endsection
