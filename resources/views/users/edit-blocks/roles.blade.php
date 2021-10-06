<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Роли доступа</h3>
        </div>
    </div>
    <div>
        <form class="form" action="{{ route('users.roles.update', $user) }}" method="POST">
            @csrf
            <div class="card-body border-top p-9">
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-lg-4 mb-6">
                            <div class="form-check form-check-custom form-check-solid">
                                <input
                                    class="form-check-input me-3"
                                    name="user_role"
                                    type="radio"
                                    value="{{ $role->id }}"
                                    id="role_{{ $role->id }}"
                                    {{ $user->hasRole($role->name) ? 'checked' : '' }}
                                />
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    <div class="fw-bolder text-gray-800">{{ $role->name }}</div>
                                    <div class="text-gray-600">{{ $role->description }}</div>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </div>
</div>
