@forelse($objects as $object)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body pt-9 pb-0">
                    @include('objects.parts._object_general_info')
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-center text-dark fw-bolder d-block my-4 fs-6">Объекты отсутствуют</p>
                </div>
            </div>
        </div>
    </div>
@endforelse

{{ $objects->links() }}
