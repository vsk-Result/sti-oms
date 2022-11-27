@extends('objects.layouts.show')

@section('object-tab-title', 'Долги')

@section('object-tab-content')
    @include('objects.modals.debt_manual_edit')

    <div class="card border-0">
        <div class="card-header border-0 justify-content-end align-items-center p-0">
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('objects.debts.exports.store', $object) }}" method="POST" class="hidden">
                        @csrf
                        <a
                            href="javascript:void(0);"
                            class="btn btn-light-primary"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                        >
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="black"></rect>
                                    <path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="black"></path>
                                    <path d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="#C4C4C4"></path>
                                </svg>
                            </span>
                            Экспорт в Excel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        <div class="col-lg-6">
            @if ($object->code === '288')

                @php
                    $contractorDebts = $object->getContractorDebts();

                    $oneAmount = 0;
                    $twoFourAmount = 0;
                    foreach($contractorDebts as $organizationId => $organization) {
                        $one = $organization['worktype'][1] ?? 0;
                        $two = $organization['worktype'][2] ?? 0;
                        $four = $organization['worktype'][4] ?? 0;
                        $seven = $organization['worktype'][7] ?? 0;
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
                                    <div class="fs-4 fw-bolder text-danger">
                                        <a
                                            target="_blank"
                                            class="text-danger"
                                            href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}&object_worktype_id%5B%5D=1"
                                        >
                                            {{ number_format($oneAmount, 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Контрагент</th>
                                <th>Сумма</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($contractorDebts as $organizationId => $organization)
                                    @php
                                        $one = $organization['worktype'][1] ?? 0;
                                        $two = $organization['worktype'][2] ?? 0;
                                        $four = $organization['worktype'][4] ?? 0;
                                        $seven = $organization['worktype'][7] ?? 0;
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
                                            <td>{{ $organization['name'] }}</td>
                                            <td class="text-danger">
                                                <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&object_worktype_id%5B%5D=1">
                                                    {{ number_format($oneTotal * $seven + $one, 2, ',', ' ') }}
                                                </a>
                                            </td>
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
                                    <div class="fs-4 fw-bolder text-danger">
                                        <a
                                            target="_blank"
                                            class="text-danger"
                                            href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}&object_worktype_id%5B%5D=2&object_worktype_id%5B%5D=4"
                                        >
                                            {{ number_format($twoFourAmount, 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Контрагент</th>
                                <th>Сумма</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($contractorDebts as $organizationId => $organization)
                                    @php
                                        $one = $organization['worktype'][1] ?? 0;
                                        $two = $organization['worktype'][2] ?? 0;
                                        $four = $organization['worktype'][4] ?? 0;
                                        $seven = $organization['worktype'][7] ?? 0;
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
                                            <td>{{ $organization['name'] }}</td>
                                            <td class="text-danger">
                                                <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&object_worktype_id%5B%5D=2&object_worktype_id%5B%5D=4">
                                                    {{ number_format($twoFourTotal * $seven + $two + $four, 2, ',', ' ') }}
                                                </a>
                                            </td>
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
                                    <div class="fs-4 fw-bolder text-danger">
                                        <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                                            {{ number_format($object->getContractorDebtsAmount(), 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2">Контрагент</th>
                                <th class="w-175px pe-2">Сумма</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($object->getContractorDebts() as $organization => $amount)
                                    @php
                                        $type = \App\Models\Debt\DebtManual::TYPE_CONTRACTOR;
                                        $organizationId = substr($organization, 0, strpos($organization, '::'));
                                        $organizationName = substr($organization, strpos($organization, '::') + 2);
                                        $debtManual = $debtManuals->where('type_id', $type)->where('organization_id', $organizationId)->first();

                                        $comment = '';
                                        if ($debtManual) {
                                            if ($debtManual->updatedBy) {
                                                $comment = 'Изменил(а) ' . $debtManual->updatedBy->name . ', ' . $debtManual->updated_at->format('d.m.Y H:i');
                                            } else {
                                                $comment = 'Создал(а) ' . $debtManual->createdBy->name . ', ' . $debtManual->created_at->format('d.m.Y H:i');
                                            }
                                        }
                                    @endphp

                                    <tr class="row-edit-debt-manual {{ $debtManual ? 'manual' : '' }}">
                                        <td class="ps-2">{{ $organizationName }}</td>
                                        <td class="text-danger d-flex justify-content-between gap-2 pe-2">
                                            @if ($debtManual)
                                                <div>
                                                    <span class="fw-boldest">{{ number_format($debtManual->amount, 2, ',', ' ') }}</span><br>
                                                    <span class="text-muted fs-8">(изменено вручную)</span>
                                                </div>
                                            @else
                                                <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}">
                                                    {{ number_format($amount, 2, ',', ' ') }}
                                                </a>
                                            @endif

                                            <a
                                                    class="edit-debt-manual d-none text-hover-gray-900"
                                                    href="javascript:void(0)"
                                                    data-organization-name="{{ $organizationName }}"
                                                    data-organization-id="{{ $organizationId }}"
                                                    data-object-id="{{ $object->id }}"
                                                    data-type-id="{{ $type }}"
                                                    data-id="{{ $debtManual->id ?? '' }}"
                                                    data-amount="{{ $debtManual->amount ?? $amount }}"
                                                    data-comment="{{ $comment }}"
                                            >
                                                <i class="fa fa-pen text-primary"></i>
                                            </a>
                                        </td>
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
                    foreach($providerDebts as $organizationId => $organization) {
                        $one = $organization['worktype'][1] ?? 0;
                        $two = $organization['worktype'][2] ?? 0;
                        $four = $organization['worktype'][4] ?? 0;
                        $seven = $organization['worktype'][7] ?? 0;
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
                                    <div class="fs-4 fw-bolder text-danger">
                                        <a
                                            target="_blank"
                                            class="text-danger"
                                            href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}&object_worktype_id%5B%5D=1"
                                        >
                                            {{ number_format($oneAmount, 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Контрагент</th>
                                <th>Сумма</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            @forelse($providerDebts as $organizationId => $organization)
                                @php
                                    $one = $organization['worktype'][1] ?? 0;
                                    $two = $organization['worktype'][2] ?? 0;
                                    $four = $organization['worktype'][4] ?? 0;
                                    $seven = $organization['worktype'][7] ?? 0;
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
                                        <td>{{ $organization['name'] }}</td>
                                        <td class="text-danger">
                                            <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&object_worktype_id%5B%5D=1">
                                                {{ number_format($oneTotal * $seven + $one, 2, ',', ' ') }}
                                            </a>
                                        </td>
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
                                    <div class="fs-4 fw-bolder text-danger">
                                        <a
                                            target="_blank"
                                            class="text-danger"
                                            href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}&object_worktype_id%5B%5D=2&object_worktype_id%5B%5D=4"
                                        >
                                            {{ number_format($twoFourAmount, 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th>Контрагент</th>
                                <th>Сумма</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            @forelse($providerDebts as $organizationId => $organization)
                                @php
                                    $one = $organization['worktype'][1] ?? 0;
                                    $two = $organization['worktype'][2] ?? 0;
                                    $four = $organization['worktype'][4] ?? 0;
                                    $seven = $organization['worktype'][7] ?? 0;
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
                                        <td>{{ $organization['name'] }}</td>
                                        <td class="text-danger">
                                            <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&object_worktype_id%5B%5D=2&object_worktype_id%5B%5D=4">
                                                {{ number_format($twoFourTotal * $seven + $two + $four, 2, ',', ' ') }}
                                            </a>
                                        </td>
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
                                    <div class="fs-4 fw-bolder">
                                        <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}">
                                            {{ number_format($object->getProviderDebtsAmount(), 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="ps-2">Контрагент</th>
                                    <th class="w-175px pe-2">Сумма</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($object->getProviderDebts() as $organization => $amount)
                                    @php
                                        $type = \App\Models\Debt\DebtManual::TYPE_PROVIDER;
                                        $organizationId = substr($organization, 0, strpos($organization, '::'));
                                        $organizationName = substr($organization, strpos($organization, '::') + 2);
                                        $debtManual = $debtManuals->where('type_id', $type)->where('organization_id', $organizationId)->first();

                                        $comment = '';
                                        if ($debtManual) {
                                            if ($debtManual->updatedBy) {
                                                $comment = 'Изменил(а) ' . $debtManual->updatedBy->name . ', ' . $debtManual->updated_at->format('d.m.Y H:i');
                                            } else {
                                                $comment = 'Создал(а) ' . $debtManual->createdBy->name . ', ' . $debtManual->created_at->format('d.m.Y H:i');
                                            }
                                        }
                                    @endphp

                                    <tr class="row-edit-debt-manual {{ $debtManual ? 'manual' : '' }}">
                                        <td class="ps-2">{{ $organizationName }}</td>
                                        <td class="text-danger d-flex justify-content-between gap-2 pe-2">
                                            @if ($debtManual)
                                                <div>
                                                    <span class="fw-boldest">{{ number_format($debtManual->amount, 2, ',', ' ') }}</span><br>
                                                    <span class="text-muted fs-8">(изменено вручную)</span>
                                                </div>
                                            @else
                                                <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}">
                                                    {{ number_format($amount, 2, ',', ' ') }}
                                                </a>
                                            @endif

                                            <a
                                                    class="edit-debt-manual d-none text-hover-gray-900"
                                                    href="javascript:void(0)"
                                                    data-organization-name="{{ $organizationName }}"
                                                    data-organization-id="{{ $organizationId }}"
                                                    data-object-id="{{ $object->id }}"
                                                    data-type-id="{{ $type }}"
                                                    data-id="{{ $debtManual->id ?? '' }}"
                                                    data-amount="{{ $debtManual->amount ?? $amount }}"
                                                    data-comment="{{ $comment }}"
                                            >
                                                <i class="fa fa-pen text-primary"></i>
                                            </a>
                                        </td>
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

@push('styles')
    <style>
        .row-edit-debt-manual:hover a.edit-debt-manual {
            display: block !important;
        }
        .row-edit-debt-manual.manual {
            background-color: cornsilk;
        }
    </style>
@endpush


@push('scripts')
    <script>
        $('.edit-debt-manual').on('click', function() {
            $('#debtManualModal').modal('show');
            $('#debtManualModal .modal-title').text(`Укажите сумму долга вручную для ${$(this).data('organization-name')}`);
            $('#debt-manual-amount').val($(this).data('amount') || '');
            $('#debt-manual-id').val($(this).data('id') || '');
            $('#debt-manual-type-id').val($(this).data('type-id') || '');
            $('#debt-manual-object-id').val($(this).data('object-id') || '');
            $('#debt-manual-object-worktype-id').val($(this).data('object-worktype-id') || '');
            $('#debt-manual-organization-id').val($(this).data('organization-id') || '');
            $('#debt-manual-comment').text($(this).data('comment') || '');
        });
    </script>
@endpush
