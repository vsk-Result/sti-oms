<div class="modal fade" tabindex="-1" id="updateITRSalaryModal">
    <div class="modal-dialog">
        <form action="{{ route('pivots.calculate_workers_cost.itr_salary.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Загрузите файл с расходами ИТР</h4>
                </div>

                <div class="modal-body">
                    <div class="form group mb-4">
                        <label class="required fs-5 fw-bold mb-2">Файл для загрузки</label>
                        <input required type="file" class="form-control form-control-solid" placeholder="" name="file" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
                        <div class="form-text">Доступные форматы:
                            <code>xls, xlsx</code>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </div>
            </div>
        </form>
    </div>
</div>
