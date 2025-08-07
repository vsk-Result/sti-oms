<div class="modal fade" tabindex="-1" id="cashAccountTransferCashModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Передача денежных средств</h4>
            </div>

            <form action="{{ route('cash_accounts.transfer_cash.store', $cashAccount) }}" method="POST" class="form">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-10 fv-row">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6">Получатель</label>
                                <div class="position-relative mb-3">
                                    <select
                                        name="receiver_id"
                                        class="form-select form-select-solid"
                                        data-control="select2"
                                        data-dropdown-parent="#cashAccountTransferCashModal"
                                    >
                                        @foreach($transferCashAccounts as $ca)
                                            <option value="{{ $ca->id }}">{{ $ca->getName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-10 fv-row">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6">Ожидаемая дата передачи средств</label>
                                <div class="position-relative mb-3">
                                    <input
                                        class="date-range-picker-single form-control form-control-lg form-control-solid"
                                        type="text"
                                        name="date"
                                        value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                        readonly
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-10 fv-row">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6">Сумма</label>
                                <div class="position-relative mb-3">
                                    <input
                                        class="amount-mask form-control form-control-lg form-control-solid"
                                        type="text"
                                        name="amount"
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-10 fv-row">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6">Дополнительная информация</label>
                                <div class="position-relative mb-3">
                                    <textarea
                                        class="form-control form-control-lg form-control-solid"
                                        rows="3"
                                        name="description"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Передать</button>
                </div>
            </form>
        </div>
    </div>
</div>
