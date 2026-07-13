<div class="modal fade" tabindex="-1" id="filesUploadModal" data-url="{{ route('objects.files.store', $object) }}">
    <div class="modal-dialog modal-lg">
        <form action="" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Добавьте новые файлы</h4>
                </div>

                <input type="hidden" name="object_id" class="form-control" value="{{ $object->id }}" />

                <div class="modal-body">
                    <form class="form" action="#" method="post">
                        <div class="form-group row">
                            <div class="dropzone dropzone-queue mb-2" id="kt_dropzonejs_example_2">
                                <div class="dropzone-panel mb-lg-0 mb-2">
                                    <a class="dropzone-select btn btn-sm btn-primary me-2">Выбрать файлы</a>
                                    <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Сохранить все</a>
                                    <a class="dropzone-remove-all btn btn-sm btn-light-primary">Отменить все</a>
                                </div>

                                <div class="dropzone-items wm-200px">
                                    <div class="dropzone-item" style="display:none">
                                        <div class="dropzone-file">
                                            <div class="dropzone-filename" title="some_image_file_name.jpg">
                                                <span data-dz-name>some_image_file_name.jpg</span>
                                                <strong>(<span data-dz-size>340kb</span>)</strong>
                                            </div>

                                            <div class="dropzone-error" data-dz-errormessage></div>
                                        </div>

                                        <div class="dropzone-progress">
                                            <div class="progress">
                                                <div
                                                    class="progress-bar bg-primary"
                                                    role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dropzone-toolbar">
                                            <span class="dropzone-start"><i class="bi bi-play-fill fs-3"></i></span>
                                            <span class="dropzone-cancel" data-dz-remove style="display: none;"><i class="bi bi-x fs-3"></i></span>
                                            <span class="dropzone-delete" data-dz-remove><i class="bi bi-x fs-1"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <span class="form-text text-muted">Максимальный размер файла 10мб</span>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </form>
    </div>
</div>
