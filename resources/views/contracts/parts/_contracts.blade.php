<tr class="subcontract-row fw-boldest">
    @php
        $mainContract->type_id = \App\Models\Contract\Contract::TYPE_ADDITIONAL;
    @endphp
    <td></td>
    <td class="px-3">Основной договор</td>
    <td></td>
    <td>{{ \App\Models\CurrencyExchangeRate::format($mainContract->getAmount($currency), $currency) }}</td>
    <td>{{ \App\Models\CurrencyExchangeRate::format($mainContract->getAvansesAmount($currency), $currency) }}</td>
    <td>{{ \App\Models\CurrencyExchangeRate::format($mainContract->getAvansesReceivedAmount($currency), $currency) }}</td>
    <td>{{ \App\Models\CurrencyExchangeRate::format($mainContract->getAvansesLeftAmount($currency), $currency) }}</td>
    <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $mainContract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($mainContract->getActsAmount($currency), $currency) }}</a></td>
    <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $mainContract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($mainContract->getActsAvasesAmount($currency), $currency) }}</a></td>
    <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $mainContract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($mainContract->getActsDepositesAmount($currency), $currency) }}</a></td>
    <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $mainContract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($mainContract->getActsNeedPaidAmount($currency), $currency) }}</a></td>
    <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $mainContract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($mainContract->getActsPaidAmount($currency), $currency) }}</a></td>
    <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $mainContract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($mainContract->getActsLeftPaidAmount($currency), $currency) }}</a></td>
    <td>{{ \App\Models\CurrencyExchangeRate::format($mainContract->getAvansesReceivedAmount($currency) - $mainContract->getActsAvasesAmount($currency), $currency) }}</td>
    <td>
        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
            <span class="svg-icon svg-icon-5 m-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                </svg>
            </span>
        </a>
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
            @can('edit contracts')
                <div class="menu-item px-3">
                    <a target="_blank" href="{{ route('contracts.edit', $mainContract) }}" class="menu-link px-3">Изменить</a>
                </div>

                <div class="menu-item px-3">
                    <form action="{{ route('contracts.destroy', $mainContract) }}" method="POST" class="hidden">
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
@foreach($contracts as $contract)
    <tr class="subcontract-row">
        <td></td>
        <td class="px-3">
            {{ $contract->getName() }}
        </td>
        <td></td>
        <td>{{ \App\Models\CurrencyExchangeRate::format($contract->getAmount($currency), $currency) }}</td>
        <td>{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesAmount($currency), $currency) }}</td>
        <td>{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesReceivedAmount($currency), $currency) }}</td>
        <td>{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesLeftAmount($currency), $currency) }}</td>
        <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $contract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsAmount($currency), $currency) }}</a></td>
        <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $contract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsAvasesAmount($currency), $currency) }}</a></td>
        <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $contract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsDepositesAmount($currency), $currency) }}</a></td>
        <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $contract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsNeedPaidAmount($currency), $currency) }}</a></td>
        <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $contract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsPaidAmount($currency), $currency) }}</a></td>
        <td><a href="{{ route('acts.index') }}?contract_id%5B%5D={{ $contract->id }}" class="show-link">{{ \App\Models\CurrencyExchangeRate::format($contract->getActsLeftPaidAmount($currency), $currency) }}</a></td>
        <td>{{ \App\Models\CurrencyExchangeRate::format($contract->getAvansesReceivedAmount($currency) - $contract->getActsAvasesAmount($currency), $currency) }}</td>
        <td>
            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                <span class="svg-icon svg-icon-5 m-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                    </svg>
                </span>
            </a>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
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
@endforeach
