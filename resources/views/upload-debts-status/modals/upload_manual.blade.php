<div class="modal fade" tabindex="-1" id="uploadManualModal">
    <div class="modal-dialog">
        <form action="{{ route('upload_debts_status.store') }}"  method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header d-flex flex-column align-items-baseline gap-2">
                    <h4 class="modal-title">Загрузка файла долгов</h4>
                    <p class="debt-name mb-0"></p>
                </div>

                <input type="hidden" name="command" class="upload-command" value="" />
                <input type="hidden" name="filename" class="upload-filename" value="" />

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
