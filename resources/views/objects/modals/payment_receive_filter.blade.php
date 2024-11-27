<div class="modal fade" tabindex="-1" id="filterPaymentReceiveModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Настройте фильтр для получения более точной информации</h4>
            </div>

            <form action="{{ request()->url() }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Период</label>
                                <input
                                    name="period"
                                    class="form-control form-control-solid date-range-picker"
                                    value="{{ request()->input('period', '') }}"
                                    autocomplete="off"
                                />

                                <div class="mt-3">
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary mb-1">2017</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2018</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2019</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2020</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2021</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2022</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2023</span>
                                    <span class="period-quick badge badge-light cursor-pointer bg-hover-secondary">2024</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Детализация</label>
                                <select
                                    name="details_type"
                                    class="form-select form-select-solid"
                                    data-control="select2"
                                    data-dropdown-parent="#filterPaymentReceiveModal"
                                >
                                    <option value="by_day" {{ request()->input('details_type') === 'by_day' ? 'selected' : '' }}>По дням</option>
                                    <option value="by_month" {{ request()->input('details_type') === 'by_month' ? 'selected' : '' }}>По месяцам</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light float-left" data-bs-dismiss="modal">Закрыть</button>
                    <a href="{{ request()->url() }}" class="btn btn-light">Сбросить</a>
                    <button type="submit" class="btn btn-primary">Применить</button>
                </div>
            </form>
        </div>
    </div>
</div>
