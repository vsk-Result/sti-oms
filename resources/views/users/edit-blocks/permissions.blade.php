<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Права доступа</h3>
        </div>
    </div>
    <div>
        <form class="form" action="{{ route('users.permissions.update', $user) }}" method="POST">
            @csrf
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <div class="col-md-12">
                        @include('partials.permissions', ['model' => $user])
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </div>
</div>
