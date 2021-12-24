@extends('objects.layouts.show')

@section('object-tab-title', 'Акты')

@section('object-tab-content')
    <div class="row g-6 g-xl-9">
        <div class="col-lg-12">
            <div class="card card-flush h-lg-100">
                <div class="card-header border-0 pt-6">
                    <div class="card-title"></div>

                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            @can('create acts')
                                <a href="{{ route('acts.create') }}?current_object_id={{ $object->id }}" class="btn btn-light-primary">
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
                                <th class="min-w-150px">Договор</th>
                                <th class="min-w-150px">Объект</th>
                                <th class="min-w-150px">Дата</th>
                                <th class="min-w-150px">Выполнено</th>
                                <th class="min-w-150px">Аванс удержан</th>
                                <th class="min-w-150px">Депозит удержан</th>
                                <th class="min-w-150px">К оплате</th>
                                <th class="min-w-150px">Оплачено</th>
                                <th class="min-w-150px">Сумма неоплаченных работ</th>
                                <th class="min-w-150px">Действие</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            @forelse($acts as $act)
                                <tr>
                                    <td class="px-3">
                                        @if(auth()->user()->can('show contracts'))
                                            <a target="_blank" href="{{ route('contracts.show', $act->contract) }}" class="show-link">{{ $act->contract->getName() }}</a>
                                        @else
                                            {{ $act->contract->getName() }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->can('show objects'))
                                            <a target="_blank" href="{{ route('objects.show', $act->object) }}" class="show-link">{{ $act->object->getName() }}</a>
                                        @else
                                            {{ $act->object->getName() }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->can('show acts'))
                                            <a target="_blank" href="{{ route('acts.show', $act) }}" class="show-link">{{ $act->getDateFormatted() }}</a>
                                        @else
                                            {{ $act->getDateFormatted() }}
                                        @endif
                                    </td>
                                    <td>{{ $act->getAmount() }}</td>
                                    <td>{{ $act->getAvansAmount() }}</td>
                                    <td>{{ $act->getDepositAmount() }}</td>
                                    <td>{{ $act->getNeedPaidAmount() }}</td>
                                    <td>{{ $act->getPaidAmount() }}</td>
                                    <td>{{ $act->getLeftPaidAmount() }}</td>
                                    <td>
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                            @can('show acts')
                                                <div class="menu-item px-3">
                                                    <a target="_blank" href="{{ route('acts.show', $act) }}" class="menu-link px-3">Посмотреть</a>
                                                </div>
                                            @endcan
                                            @can('edit acts')
                                                <div class="menu-item px-3">
                                                    <a target="_blank" href="{{ route('acts.edit', $act) }}" class="menu-link px-3">Изменить</a>
                                                </div>

                                                <div class="menu-item px-3">
                                                    <form action="{{ route('acts.destroy', $act) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a
                                                            href="#"
                                                            class="menu-link px-3 text-danger"
                                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить акт?')) {this.closest('form').submit();}"
                                                        >
                                                            Удалить
                                                        </a>
                                                    </form>
                                                </div>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <p class="text-center text-dark fw-bolder d-block my-4 fs-6">
                                            Акты отсутствуют
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
