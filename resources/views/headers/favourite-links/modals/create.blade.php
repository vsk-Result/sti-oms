<div class="modal fade" tabindex="-1" id="createFavouriteLinkModal" data-store-favourite-link-url="{{ route('favourite_links.store') }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавить текущую ссылку в быстрый переход</h4>
            </div>

            <div class="modal-body">
                <div class="fv-row">
                    <div class="mb-1">
                        <label class="form-label fw-bolder text-dark fs-6">Название ссылки</label>
                        <div class="position-relative mb-3">
                            <input
                                class="form-control form-control-lg form-control-solid"
                                type="text"
                                name="favourite_link_name"
                                autocomplete="off"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Закрыть</button>
                <button id="create-favourite-link-submit" type="button" class="btn btn-primary">Добавить</button>
            </div>
        </div>
    </div>
</div>
