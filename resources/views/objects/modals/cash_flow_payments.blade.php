<div class="modal fade" tabindex="-1" id="cashFlowPaymentsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Расходы, добавленные вручную</h4>
            </div>

            <div class="modal-body">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="text-center min-w-125px ps-2">Контрагент</th>
                            <th class="text-center min-w-125px">Категория</th>
                            <th class="text-center min-w-125px">Дата</th>
                            <th class="text-center min-w-125px">Сумма</th>
                            <th class="text-center min-w-100px"></th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-bold">
                        @foreach($cashFlowPayments as $cashFlowPayment)
                            <tr>
                                <td class="ps-2">{{ $cashFlowPayment->organization->name }}</td>
                                <td class="text-center">{{ $cashFlowPayment->getCategory() }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($cashFlowPayment->date)->format('d.m.Y') }}</td>
                                <td class="text-center">{{ \App\Models\CurrencyExchangeRate::format($cashFlowPayment->amount) }}</td>

                                <td class="text-center">
                                    <form action="{{ route('objects.cash_flow.payments.destroy', [$object, $cashFlowPayment]) }}" method="POST" class="hidden">
                                        @csrf
                                        <a
                                            href="{{ route('objects.cash_flow.payments.destroy', [$object, $cashFlowPayment]) }}"
                                            class="text-danger"
                                            onclick="event.preventDefault(); if (confirm('Вы действительно хотите удалить расход?')) {this.closest('form').submit();}"
                                        >
                                            Удалить
                                        </a>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
