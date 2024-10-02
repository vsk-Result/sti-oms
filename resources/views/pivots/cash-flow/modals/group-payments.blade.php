<div class="modal fade" tabindex="-1" id="groupPaymentsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Группировка планов по расходам</h4>
            </div>

            <div class="modal-body">

                <form action="{{ route('pivots.cash_flow.plan_payments.group.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Новая группа</label>
                                <input name="name" class="form-control form-control-solid form-control-lg" value=""/>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary mt-2">Сохранить</button>
                </form>

                @if ($planPaymentGroups->count() > 0)
                    <hr>
                    <form action="{{ route('pivots.cash_flow.plan_payments.group.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                @foreach($planPaymentGroups as $group)
                                    <div class="form-group mb-6">
                                        <label class="form-label">{{ $group->name }}</label>
                                        <select
                                                name="payments[{{ $group->id }}][]"
                                                data-control="select2"
                                                class="form-select form-select-solid"
                                                multiple
                                        >
                                            @foreach($CFPlanPayments as $p)
                                                <option value="{{ $p->id }}" {{ $p->id == in_array($p->id, $group->payments->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm mt-8">Сохранить</button>
                        <button type="button" class="btn btn-sm btn-light mt-8" data-bs-dismiss="modal">Закрыть</button>

                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
