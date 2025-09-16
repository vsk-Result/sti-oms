<div class="modal fade" tabindex="-1" id="cashAccountClosePeriodsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Закрытые периоды кассы {{ $cashAccount->name }}</h4>
            </div>

            <div class="modal-body">
                <h3 class="mb-4">Закрытые периоды: </h3>

                @if ($closePeriods->count() > 0)
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="text-center min-w-125px ps-2">Месяц</th>
                                <th class="text-center min-w-125px">Кол-во оплат</th>
                                <th class="text-center min-w-125px">Сумма оплат</th>
                                <th class="text-center min-w-100px"></th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold">
                            @foreach($closePeriods as $closePeriod)
                                <tr>
                                    <td class="ps-2">{{ translate_year_month_word(\Carbon\Carbon::parse($closePeriod->period)->format('F Y')) }}</td>
                                    <td class="text-center">{{ $closePeriod->payments_count }}</td>
                                    <td class="text-center">{{ \App\Models\CurrencyExchangeRate::format($closePeriod->payments_amount) }}</td>

                                    <td class="text-center">
                                        <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">Действия
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="black" />
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-150px py-4" data-kt-menu="true">
                                            @if ($cashAccount->isCurrentResponsible())
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('cash_accounts.close_periods.update', [$cashAccount, $closePeriod]) }}" class="text-dark menu-link px-3">Обновить</a>
                                                </div>
                                            @endif

                                            <div class="menu-item px-3">
                                                <form action="{{ route('cash_accounts.close_periods.export.store', [$cashAccount, $closePeriod]) }}" method="POST" class="hidden">
                                                    @csrf
                                                    <a
                                                            href="javascript:void(0);"
                                                            class="text-success menu-link px-3"
                                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                                    >
                                                        Экспорт в Excel
                                                    </a>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="fs-6">Закрытые периоды отсутствуют</p>
                @endif

                @if ($cashAccount->isCurrentResponsible())
                    <h3 class="mb-4 mt-10">Можно закрыть: </h3>

                    @if (count($periodsToClose) > 0)
                        @foreach($periodsToClose as $period)
                            <form action="{{ route('cash_accounts.close_periods.store', $cashAccount) }}?period={{ $period }}" method="POST" class="hidden">
                                @csrf
                                <a
                                    href="#"
                                    class="btn btn-light mt-1"
                                    onclick="event.preventDefault(); if (confirm('Вы уверены, что хотите закрыть данный период?')) {this.closest('form').submit();}"
                                >
                                    {{ translate_year_month($period) }}
                                </a>
                            </form>
                        @endforeach
                    @else
                        <p class="fs-6">Периоды к закрытию отсутствуют</p>
                    @endif
                @endif
            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
