@extends('objects.layouts.show')

@section('object-tab-title', 'Договора')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header border-0 pt-6">
                    @php
                        $totalAmount = 0;
                        $totalAvansesReceivedAmount = 0;
                        $totalActsPaidAmount = 0;
                        $totalAvansesLeftAmount = 0;
                        $totalActsLeftPaidAmount = 0;
                        $totalActsDepositesAmount = 0;
                    @endphp

                    @foreach($object->contracts as $contract)
                        @php
                            $totalAmount += $contract->getAmount(false);
                            $totalAvansesReceivedAmount += $contract->getAvansesReceivedAmount(false);
                            $totalActsPaidAmount += $contract->getActsPaidAmount(false);
                            $totalAvansesLeftAmount += $contract->getAvansesLeftAmount(false);
                            $totalActsLeftPaidAmount += $contract->getActsLeftPaidAmount(false);
                            $totalActsDepositesAmount += $contract->getActsDepositesAmount(false);
                        @endphp
                    @endforeach
                    <div class="card-title">
                        <div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder">{{ number_format($totalAmount, 2, '.', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Сумма договоров</div>
                            </div>

                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-success">{{ number_format($totalAvansesReceivedAmount, 2, '.', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Сумма оплаченных авансов</div>
                            </div>
                        </div>

                        <div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-success">{{ number_format($totalActsPaidAmount, 2, '.', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Сумма оплаченных актов</div>
                            </div>

                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-success">{{ number_format($totalAvansesLeftAmount, 2, '.', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Сумма аванса к получению</div>
                            </div>
                        </div>

                        <div>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-success">{{ number_format($totalActsLeftPaidAmount, 2, '.', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Долг подписанных актов</div>
                            </div>

                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bolder text-success">{{ number_format($totalActsDepositesAmount, 2, '.', ' ') }}</div>
                                </div>
                                <div class="fw-bold fs-6 text-gray-400">Долг ГУ</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            @can('create contracts')
                                <a href="{{ route('contracts.create') }}?current_object_id={{ $object->id }}" class="btn btn-light-primary">
                                <span class="svg-icon svg-icon-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                    </svg>
                                </span>
                                    Новый договор
                                </a>
                            @endcan

                            @can('create acts')
                                <a href="{{ route('acts.create') }}?current_object_id={{ $object->id }}" class="btn btn-light-primary ms-4">
                            <span class="svg-icon svg-icon-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="black"></rect>
                                    <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="black"></rect>
                                    <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="black"></rect>
                                </svg>
                            </span>
                                    Новый акт
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-200px">Номер</th>
                                <th class="min-w-150px">Объект</th>
                                <th class="min-w-150px">Сумма</th>
                                <th class="min-w-150px">Сумма аванса</th>
                                <th class="min-w-150px">Сумма полученного аванса</th>
                                <th class="min-w-150px">Сумма аванса к получению</th>
                                <th class="min-w-150px">Выполнено по актам</th>
                                <th class="min-w-150px">Аванс удержан по актам</th>
                                <th class="min-w-150px">Депозит удержан по актам</th>
                                <th class="min-w-150px">К оплате по актам</th>
                                <th class="min-w-150px">Оплачено по актам</th>
                                <th class="min-w-150px">Сумма неоплаченных работ по актам</th>
                                <th class="min-w-150px rounded-end pe-4">Действия</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            @forelse($contracts as $contract)
                                <tr style="background-color: whitesmoke;">
                                    <td class="px-3">
                                        @if(auth()->user()->can('show contracts'))
                                            <a href="{{ route('contracts.show', $contract) }}" class="show-link">{{ $contract->getName() }}</a>
                                        @else
                                            {{ $contract->getName() }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->can('show objects'))
                                            <a href="{{ route('objects.show', $contract->object) }}" class="show-link">{{ $contract->object->getName() }}</a>
                                        @else
                                            {{ $contract->object->getName() }}
                                        @endif
                                    </td>
                                    <td>{{ $contract->getAmount() }}</td>
                                    <td>{{ $contract->getAvansesAmount() }}</td>
                                    <td>{{ $contract->getAvansesReceivedAmount() }}</td>
                                    <td>{{ $contract->getAvansesLeftAmount() }}</td>
                                    <td>{{ $contract->getActsAmount() }}</td>
                                    <td>{{ $contract->getActsAvasesAmount() }}</td>
                                    <td>{{ $contract->getActsDepositesAmount() }}</td>
                                    <td>{{ $contract->getActsNeedPaidAmount() }}</td>
                                    <td>{{ $contract->getActsPaidAmount() }}</td>
                                    <td>{{ $contract->getActsLeftPaidAmount() }}</td>
                                    <td>
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                            @can('show contracts')
                                                <div class="menu-item px-3">
                                                    <a target="_blank" href="{{ route('contracts.show', $contract) }}" class="menu-link px-3">Посмотреть</a>
                                                </div>
                                            @endcan
                                            @can('edit contracts')
                                                <div class="menu-item px-3">
                                                    <a target="_blank" href="{{ route('contracts.edit', $contract) }}" class="menu-link px-3">Изменить</a>
                                                </div>

                                                <div class="menu-item px-3">
                                                    <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                            href="#"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить договор?')) {this.closest('form').submit();}"
                                                        >
                                                            Удалить
                                                        </a>
                                                    </form>
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                @foreach($contract->children as $subContract)
                                    <tr>
                                        <td class="px-3 ps-10">
                                            @if(auth()->user()->can('show contracts'))
                                                <a target="_blank" href="{{ route('contracts.show', $subContract) }}" class="show-link">{{ $subContract->getName() }}</a>
                                            @else
                                                {{ $subContract->getName() }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(auth()->user()->can('show objects'))
                                                <a target="_blank" href="{{ route('objects.show', $subContract->object) }}" class="show-link">{{ $subContract->object->getName() }}</a>
                                            @else
                                                {{ $subContract->object->getName() }}
                                            @endif
                                        </td>
                                        <td>{{ $subContract->getAmount() }}</td>
                                        <td>{{ $subContract->getAvansesAmount() }}</td>
                                        <td>{{ $subContract->getAvansesReceivedAmount() }}</td>
                                        <td>{{ $subContract->getAvansesLeftAmount() }}</td>
                                        <td>{{ $subContract->getActsAmount() }}</td>
                                        <td>{{ $subContract->getActsAvasesAmount() }}</td>
                                        <td>{{ $subContract->getActsDepositesAmount() }}</td>
                                        <td>{{ $subContract->getActsNeedPaidAmount() }}</td>
                                        <td>{{ $subContract->getActsPaidAmount() }}</td>
                                        <td>{{ $subContract->getActsLeftPaidAmount() }}</td>
                                        <td>
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                                <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                </svg>
                                            </span>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                                @can('show contracts')
                                                    <div class="menu-item px-3">
                                                        <a target="_blank" href="{{ route('contracts.show', $subContract) }}" class="menu-link px-3">Посмотреть</a>
                                                    </div>
                                                @endcan
                                                @can('edit contracts')
                                                    <div class="menu-item px-3">
                                                        <a target="_blank" href="{{ route('contracts.edit', $subContract) }}" class="menu-link px-3">Изменить</a>
                                                    </div>

                                                    <div class="menu-item px-3">
                                                        <form action="{{ route('contracts.destroy', $subContract) }}" method="POST" class="hidden">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a
                                                                href="#"
                                                                class="menu-link px-3 text-danger"
                                                                onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить договор?')) {this.closest('form').submit();}"
                                                            >
                                                                Удалить
                                                            </a>
                                                        </form>
                                                    </div>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="13">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Договора отсутствуют
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
    </div>
@endsection
