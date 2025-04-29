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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Объект</label>
                                <select
                                    name="object_id"
                                    data-control="select2"
                                    class="form-select form-select-solid"
                                >
                                    <option value="null" selected>Не указан</option>
                                    @foreach($objectList as $o)
                                        <option value="{{ $o->id }}">{{ $o->getName() }}</option>
                                    @endforeach
                                </select>
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
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group mb-6">
                                                <div class="d-flex flex-row justify-content-between">
                                                    <label class="form-label">{{ $group->name }}</label>
                                                    <a href="{{ route('pivots.cash_flow.plan_payments.group.destroy', $group) }}" class="text-danger">удалить</a>
                                                </div>
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
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-6">
                                                <label class="form-label">Объект</label>
                                                <select
                                                    name="objects[{{ $group->id }}]"
                                                    data-control="select2"
                                                    class="form-select form-select-solid"
                                                >
                                                    <option value="null" {{ is_null($group->object_id) ? 'selected' : '' }}>Не указан</option>
                                                    @foreach($objectList as $o)
                                                        <option value="{{ $o->id }}" {{ $group->object_id === $o->id ? 'selected' : '' }}>{{ $o->getName() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
