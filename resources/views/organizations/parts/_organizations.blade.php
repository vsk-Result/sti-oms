<table class="table table-hover align-middle table-row-dashed fs-6 gy-5">
    <thead>
    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
        <th class="min-w-125px">Контрагент</th>
        <th class="min-w-125px">ИНН</th>
        <th class="min-w-125px">КПП</th>
        <th class="min-w-125px">Статус</th>
        <th class="text-end min-w-100px">Действия</th>
    </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold">
        @foreach($organizations as $organization)
            <tr>
                <td>{{ $organization->name }}</td>
                <td>{{ $organization->inn }}</td>
                <td>{{ $organization->kpp }}</td>
                <td>@include('partials.status', ['status' => $organization->getStatus()])</td>
                <td class="text-end">
                    <a href="javascript:void(0)" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                        <span class="svg-icon svg-icon-5 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                            </svg>
                        </span>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-175px py-4" data-kt-menu="true">
                        @can('edit organizations')
                            <div class="menu-item px-3">
                                <a href="{{ route('organizations.transfer_payments.create', $organization) }}" class="menu-link px-3">Перенести оплаты</a>
                            </div>
                        @endcan
                        @can('edit organizations')
                            <div class="menu-item px-3">
                                <a href="{{ route('organizations.edit', $organization) }}" class="menu-link px-3">Изменить</a>
                            </div>

                            <div class="menu-item px-3">
                                <form action="{{ route('organizations.destroy', $organization) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                    <a
                                        href="{{ route('organizations.destroy', $organization) }}"
                                        class="menu-link px-3 text-danger"
                                        onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить контрагента?')) {this.closest('form').submit();}"
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
    </tbody>
</table>

{{ $organizations->links() }}
