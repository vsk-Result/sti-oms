@extends('objects.layouts.show')

@section('object-tab-title', 'Долги')

@section('object-tab-content')
    @include('objects.modals.debt_manual_create')
    @include('objects.modals.debt_manual_edit')
    @include('objects.modals.debt_upload_manual')
    @inject('pivotObjectDebtService', 'App\Services\PivotObjectDebtService')

    @php
        $debts = $pivotObjectDebtService->getPivotDebtForObject($object->id);
    @endphp

    <div class="card border-0">
        <div class="card-header border-0 justify-content-end align-items-center p-0">
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <form action="{{ route('objects.debts.exports.store', $object) }}" method="POST" class="hidden me-3">
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

                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#debtCreateManualModal" class="btn btn-light-primary">
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                            </svg>
                        </span>
                        Новый долг
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 g-xl-9">
        <div class="col-lg-6">
            @if ($object->code === '288')

                @php
                    $contractorDebts = $debts['contractor']->debts;
                    $oneAmount = 0;
                    $twoFourAmount = 0;
                    foreach($contractorDebts as $organizationId => $organization) {
                        $one = $organization->worktype->{1} ?? 0;
                        $two = $organization->worktype->{2} ?? 0;
                        $four = $organization->worktype->{4} ?? 0;
                        $seven = $organization->worktype->{7} ?? 0;
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
                                @php
                                    $sortedDebts = [];
                                @endphp

                                @foreach($contractorDebts as $organizationId => $organization)
                                    @php
                                        $one = $organization->worktype->{1} ?? 0;
                                        $two = $organization->worktype->{2} ?? 0;
                                        $four = $organization->worktype->{4} ?? 0;
                                        $seven = $organization->worktype->{7} ?? 0;
                                        $total = $one + $two + $four;
                                        $oneTotal = 0;
                                        $twoFourTotal = 0;
                                        if ($total !== 0) {
                                            $oneTotal = $one / $total;
                                            $twoFourTotal = ($two + $four) / $total;
                                        } elseif ($total === 0 && $seven !== 0) {
                                            $twoFourTotal = 1;
                                        }

                                        if (($oneTotal * $seven + $one) < 0) {
                                            $sortedDebts[$oneTotal * $seven + $one] = ['name' => $organization->name, 'id' => $organizationId];
                                        }
                                    @endphp
                                @endforeach

                                @php
                                    ksort($sortedDebts, SORT_NUMERIC);
                                @endphp

                                @forelse($sortedDebts as $amount => $info)
                                    <tr>
                                        <td>{{ $info['name'] }}</td>
                                        <td class="text-danger">
                                            <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $info['id'] }}&object_worktype_id%5B%5D=1">
                                                {{ number_format($amount, 2, ',', ' ') }}
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
                                @php
                                    $sortedDebts = [];
                                @endphp

                                @foreach($contractorDebts as $organizationId => $organization)
                                    @php
                                        $one = $organization->worktype->{1} ?? 0;
                                        $two = $organization->worktype->{2} ?? 0;
                                        $four = $organization->worktype->{4} ?? 0;
                                        $seven = $organization->worktype->{7} ?? 0;
                                        $total = $one + $two + $four;
                                        $oneTotal = 0;
                                        $twoFourTotal = 0;
                                        if ($total !== 0) {
                                            $oneTotal = $one / $total;
                                            $twoFourTotal = ($two + $four) / $total;
                                        } elseif ($total === 0 && $seven !== 0) {
                                            $twoFourTotal = 1;
                                        }

                                        if (($twoFourTotal * $seven + $two + $four) < 0) {
                                            $sortedDebts[$twoFourTotal * $seven + $two + $four] = ['name' => $organization->name, 'id' => $organizationId];
                                        }
                                    @endphp
                                @endforeach

                                @php
                                    ksort($sortedDebts, SORT_NUMERIC);
                                @endphp

                                @forelse($sortedDebts as $amount => $info)
                                    <tr>
                                        <td>{{ $info['name'] }}</td>
                                        <td class="text-danger">
                                            <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $info['id'] }}&object_worktype_id%5B%5D=2&object_worktype_id%5B%5D=4">
                                                {{ number_format($amount, 2, ',', ' ') }}
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
            @else
                <div class="card card-flush mb-4">
                    <div class="card-header mt-6">
                        <div class="card-title justify-content-between w-100">
                            <div class="d-flex flex-column">
                                <h3 class="fw-bolder mb-1">Долг подрядчикам</h3>
                                <p class="text-muted fs-7">Обновлено {{ $contractorsUpdated }}</p>
                            </div>

                            @if (in_array($object->code, App\Models\Object\BObject::getCodesForContractorImportDebts()))
                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">
                                        <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                    </svg>
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-250px py-4" data-kt-menu="true">
                                    <div class="menu-item px-3">
                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#debtUploadManualModal" class="menu-link px-3">Загрузить долги вручную</a>
                                    </div>
                                    @if ($hasObjectImportLink)
                                        <div class="menu-item px-3">
                                            <a href="{{ route('files.download', ['file' => base64_encode($hasObjectImportLink)]) }}" class="menu-link px-3 text-primary">Скачать детализацию долгов</a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="card-toolbar justify-content-end" style="width: 100%">
                            @if ($hasObjectImport)
                                @php
                                    $ds = \App\Models\Debt\Debt::where('import_id', $hasObjectImportId)->where('type_id', \App\Models\Debt\Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->get();
                                @endphp

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bolder text-danger">
                                            <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                                                {{ number_format($ds->sum('guarantee'), 2, ',', ' ') }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Итого ГУ</div>
                                </div>


                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bolder text-danger">
                                            <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                                                {{ number_format($ds->sum('avans'), 2, ',', ' ') }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Итого авансы</div>
                                </div>
                            @endif

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bolder text-danger">
                                            <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                                                {{ number_format($debts['contractor']->total_amount, 2, ',', ' ') }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="fw-bold fs-6 text-gray-400">Итого долг</div>
                                </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="ps-2">Контрагент</th>
                                @if ($hasObjectImport)
                                    <th class="w-150px text-end">ГУ</th>
                                    <th class="w-150px text-end pe-2">Авансы к оплате</th>
                                    <th class="w-175px text-end">Долг за СМР</th>
                                @else
                                    <th class="w-175px text-end pe-2">Сумма</th>
                                @endif
                                <th></th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($debts['contractor']->debts as $organization => $amount)
                                    @php
                                        $type = \App\Models\Debt\DebtManual::TYPE_CONTRACTOR;
                                        $organizationId = substr($organization, 0, strpos($organization, '::'));
                                        $organizationName = substr($organization, strpos($organization, '::') + 2);
                                        $debtManual = $debtManuals->where('type_id', $type)->where('organization_id', $organizationId)->first();

                                        $avans = 0;
                                        $guarantee = 0;
                                        if ($hasObjectImport) {
                                            $avans = $ds->where('organization_id', $organizationId)->sum('avans');
                                            $guarantee = $ds->where('organization_id', $organizationId)->sum('guarantee');
                                        }

                                        $comment = '';
                                        if ($debtManual) {
                                            if ($debtManual->updatedBy) {
                                                $comment = 'Изменил(а) ' . $debtManual->updatedBy->name . ', ' . $debtManual->updated_at->format('d.m.Y H:i');
                                            } else {
                                                $comment = 'Создал(а) ' . $debtManual->createdBy->name . ', ' . $debtManual->created_at->format('d.m.Y H:i');
                                            }

                                            if ($hasObjectImport) {
                                                $avans = $debtManual->avans;
                                                $guarantee = $debtManual->guarantee;
                                            }
                                        }
                                    @endphp

                                    <tr class="row-edit-debt-manual {{ $debtManual ? 'manual' : '' }}">
                                        <td class="ps-2">{{ $organizationName }}</td>
                                        @if ($hasObjectImport)
                                            @if ($debtManual)
                                                <td class="text-danger text-end pe-2">
                                                    <span class="fw-boldest text-end">{{ number_format($guarantee, 2, ',', ' ') }}</span>
                                                </td>
                                                <td class="text-danger text-end pe-2">
                                                    <span class="fw-boldest text-end">{{ number_format($avans, 2, ',', ' ') }}</span>
                                                </td>
                                            @else
                                                <td class="text-danger text-end pe-2">
                                                    <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                                                        {{ number_format($guarantee, 2, ',', ' ') }}
                                                    </a>
                                                </td>
                                                <td class="text-danger text-end pe-2">
                                                    <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                                                        {{ number_format($avans, 2, ',', ' ') }}
                                                    </a>
                                                </td>
                                            @endif
                                        @endif
                                        <td class="text-danger text-end pe-2">
                                            @if ($debtManual)
                                                <div class="text-end">
                                                    <span class="fw-boldest text-end">{{ number_format($debtManual->amount, 2, ',', ' ') }}</span><br>
                                                    <span class="text-muted fs-8">(изменено вручную)</span>
                                                </div>
                                            @else
                                                <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_CONTRACTOR }}">
                                                    {{ number_format($amount, 2, ',', ' ') }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a
                                                class="edit-debt-manual d-none text-hover-gray-900"
                                                href="javascript:void(0)"
                                                data-organization-name="{{ $organizationName }}"
                                                data-organization-id="{{ $organizationId }}"
                                                data-object-id="{{ $object->id }}"
                                                data-type-id="{{ $type }}"
                                                data-id="{{ $debtManual->id ?? '' }}"
                                                data-amount="{{ $debtManual->amount ?? $amount }}"
                                                data-avans="{{ $debtManual->avans ?? $avans }}"
                                                data-guarantee="{{ $debtManual->guarantee ?? $guarantee }}"
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

                <div class="card card-flush h-lg-100">
                    <div class="card-header mt-6 align-items-baseline">
                        <div class="card-title">
                            <div class="d-flex flex-column">
                                <h3 class="fw-bolder mb-1">Долг за услуги</h3>
                                <p class="text-muted fs-7">Обновлено {{ $serviceUpdated }}</p>
                            </div>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-danger">
                                        <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_SERVICE }}">
                                            {{ number_format($debts['service']->total_amount, 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого долг</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="ps-2">Контрагент</th>
                                    <th class="w-175px text-end pe-2">Сумма</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @forelse($debts['service']->debts as $organization => $amount)
                                    @php
                                        $organizationId = substr($organization, 0, strpos($organization, '::'));
                                        $organizationName = substr($organization, strpos($organization, '::') + 2);
                                    @endphp

                                    <tr>
                                        <td class="ps-2">{{ $organizationName }}</td>
                                        <td class="text-danger text-end pe-2">
                                            <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $organizationId }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_SERVICE }}">
                                                {{ number_format($amount, 2, ',', ' ') }}
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
                    $providerDebts = $debts['provider']->debts;

                    $oneAmount = 0;
                    $twoFourAmount = 0;
                    foreach($providerDebts as $organizationId => $organization) {
                        $one = $organization->worktype->{1} ?? 0;
                        $two = $organization->worktype->{2} ?? 0;
                        $four = $organization->worktype->{4} ?? 0;
                        $seven = $organization->worktype->{7} ?? 0;
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
                                    $one = $organization->worktype->{1} ?? 0;
                                    $two = $organization->worktype->{2} ?? 0;
                                    $four = $organization->worktype->{4} ?? 0;
                                    $seven = $organization->worktype->{7} ?? 0;
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
                                        <td>{{ $organization->name }}</td>
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
                                    $one = $organization->worktype->{1} ?? 0;
                                    $two = $organization->worktype->{2} ?? 0;
                                    $four = $organization->worktype->{4} ?? 0;
                                    $seven = $organization->worktype->{7} ?? 0;
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
                                        <td>{{ $organization->name }}</td>
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
                            <p class="text-muted fs-7">Обновлено {{ $providersUpdated }}</p>
                        </div>

                        <div class="card-toolbar">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder">
                                        <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}">
                                            {{ number_format($debts['provider']->fix_amount, 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого (фикс)</div>
                            </div>

                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder">
                                        <a target="_blank" class="text-danger" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}">
                                            {{ number_format($debts['provider']->float_amount, 2, ',', ' ') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Итого (изм)</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-9 pt-0">
                        <table class="table table-hover align-middle table-row-dashed fs-6">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="ps-2">Контрагент</th>
                                    <th class="w-175px pe-2 text-end">Сумма (фикс)</th>
                                    <th class="w-175px pe-2 text-end">Сумма (изм)</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @php
                                    $providerDebts = [];
                                    foreach ($debts['provider']->debts_fix as $debt) {
                                        $organizationName = $debt->organization->name;
                                        if (!isset($providerDebts[$organizationName])) {
                                            $providerDebts[$organizationName] = [
                                                'fix' => 0,
                                                'float' => 0,
                                                'organization_id' => $debt->organization_id,
                                            ];
                                        }

                                        $providerDebts[$organizationName]['fix'] += $debt->amount;
                                    }
                                    foreach ($debts['provider']->debts_float as $debt) {
                                        $organizationName = $debt->organization->name;
                                        if (!isset($providerDebts[$organizationName])) {
                                            $providerDebts[$organizationName] = [
                                                'fix' => 0,
                                                'float' => 0,
                                                'organization_id' => $debt->organization_id,
                                            ];
                                        }

                                        $providerDebts[$organizationName]['float'] += $debt->amount;
                                    }
                                @endphp
                                @forelse($providerDebts as $organizationName => $debtAmount)
{{--                                    @php--}}
{{--                                        $type = \App\Models\Debt\DebtManual::TYPE_PROVIDER;--}}
{{--                                        $organizationId = substr($organization, 0, strpos($organization, '::'));--}}
{{--                                        $organizationName = substr($organization, strpos($organization, '::') + 2);--}}
{{--                                        $debtManual = $debtManuals->where('type_id', $type)->where('organization_id', $organizationId)->first();--}}

{{--                                        $comment = '';--}}
{{--                                        if ($debtManual) {--}}
{{--                                            if ($debtManual->updatedBy) {--}}
{{--                                                $comment = 'Изменил(а) ' . $debtManual->updatedBy->name . ', ' . $debtManual->updated_at->format('d.m.Y H:i');--}}
{{--                                            } else {--}}
{{--                                                $comment = 'Создал(а) ' . $debtManual->createdBy->name . ', ' . $debtManual->created_at->format('d.m.Y H:i');--}}
{{--                                            }--}}
{{--                                        }--}}
{{--                                    @endphp--}}

{{--                                    <tr class="row-edit-debt-manual {{ $debtManual ? 'manual' : '' }}">--}}
                                    <tr class="row-edit-debt-manual">
                                        <td class="ps-2">{{ $organizationName }}</td>
{{--                                        <td class="text-danger d-flex justify-content-between gap-2 pe-2">--}}
                                        <td class="text-danger text-end">
{{--                                            @if ($debtManual)--}}
{{--                                                <div>--}}
{{--                                                    <span class="fw-boldest">{{ number_format($debtManual->amount, 2, ',', ' ') }}</span><br>--}}
{{--                                                    <span class="text-muted fs-8">(изменено вручную)</span>--}}
{{--                                                </div>--}}
{{--                                            @else--}}
                                                <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $debtAmount['organization_id'] }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}">
                                                    {{ number_format($debtAmount['fix'], 2, ',', ' ') }}
                                                </a>
{{--                                            @endif--}}

{{--                                            <a--}}
{{--                                                    class="edit-debt-manual d-none text-hover-gray-900"--}}
{{--                                                    href="javascript:void(0)"--}}
{{--                                                    data-organization-name="{{ $organizationName }}"--}}
{{--                                                    data-organization-id="{{ $organizationId }}"--}}
{{--                                                    data-object-id="{{ $object->id }}"--}}
{{--                                                    data-type-id="{{ $type }}"--}}
{{--                                                    data-id="{{ $debtManual->id ?? '' }}"--}}
{{--                                                    data-amount="{{ $debtManual->amount ?? $amount }}"--}}
{{--                                                    data-avans="{{ $debtManual->avans ?? 0 }}"--}}
{{--                                                    data-guarantee="{{ $debtManual->guarantee ?? 0 }}"--}}
{{--                                                    data-comment="{{ $comment }}"--}}
{{--                                            >--}}
{{--                                                <i class="fa fa-pen text-primary"></i>--}}
{{--                                            </a>--}}
                                        </td>
                                        <td class="text-danger text-end">
                                            <a target="_blank" class="show-link" href="{{ route('debts.index') }}?object_id%5B%5D={{ $object->id }}&organization_id%5B%5D={{ $debtAmount['organization_id'] }}&type_id%5B%5D={{ \App\Models\Debt\Debt::TYPE_PROVIDER }}">
                                                {{ number_format($debtAmount['float'], 2, ',', ' ') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
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
        $(function() {
            $('#organization-select').select2({
                sorter: function(data) {
                    return data.sort(function(a, b) {
                        return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                    });
                },
                ajax: {
                    url: '/organizations?type=select',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            search: params.term,
                            objects: ''
                        };
                    },
                    processResults: function (data) {
                        const results = [];
                        $.each(data.organizations, function(id, text) {
                            results.push({id, text})
                        });
                        return {results};
                    }
                }
            });
        });

        $('.edit-debt-manual').on('click', function() {
            $('#debtManualModal').modal('show');
            $('#debtManualModal .modal-title').text(`Укажите сумму долга вручную для ${$(this).data('organization-name')}`);
            $('#debt-manual-amount').val($(this).data('amount') || '');
            $('#debt-manual-avans').val($(this).data('avans') || '');
            $('#debt-manual-guarantee').val($(this).data('guarantee') || '');
            $('#debt-manual-id').val($(this).data('id') || '');
            $('#debt-manual-type-id').val($(this).data('type-id') || '');
            $('#debt-manual-object-id').val($(this).data('object-id') || '');
            $('#debt-manual-object-worktype-id').val($(this).data('object-worktype-id') || '');
            $('#debt-manual-organization-id').val($(this).data('organization-id') || '');
            $('#debt-manual-comment').text($(this).data('comment') || '');
        });
    </script>
@endpush
