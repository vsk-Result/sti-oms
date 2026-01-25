<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Доступ к объектам</h3>
        </div>
    </div>
    <div>
        <form class="form" action="{{ route('users.objects.update', $user) }}" method="POST">
            @csrf
            <div class="card-body border-top p-9">
                <div class="row mb-5">
                    <div class="col-md-12 fv-row">
                        <div class="mb-10 fv-row">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6">Объекты</label>
                                <select name="object_id[]" class="form-select form-select-solid" data-control="select2" multiple>
                                    @foreach($objects as $object)
                                        <option value="{{ $object->id }}" {{ $user->objects->where('id', $object->id)->first() ? 'selected' : '' }}>{{ $object->getName() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </div>
</div>
