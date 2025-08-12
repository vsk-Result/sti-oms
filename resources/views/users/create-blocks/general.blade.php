<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Основное</h3>
        </div>
    </div>
    <div>
        <form class="form" action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Имя Фамилия</label>
                    <div class="col-lg-8 fv-row">
                        <input
                            type="text"
                            name="name"
                            class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            required
                        />
                        @if ($errors->has('name'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div>{{ implode(' ', $errors->get('name')) }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Email</label>
                    <div class="col-lg-8 fv-row">
                        <input
                            type="text"
                            name="email"
                            class="form-control form-control-lg form-control-solid {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            required
                        />
                        @if ($errors->has('email'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div>{{ implode(' ', $errors->get('email')) }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Номер телефона</label>
                    <div class="col-lg-8 fv-row">
                        <input
                            type="text"
                            name="phone"
                            class="form-control form-control-lg form-control-solid  {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                            value="{{ old('phone') }}"
                        />
                        @if ($errors->has('phone'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div>{{ implode(' ', $errors->get('phone')) }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">ID пользователя в CRM</label>
                    <div class="col-lg-8 fv-row">
                        <input
                            type="text"
                            name="crm_user_id"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('crm_user_id') }}"
                        />
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Пароль</label>
                    <div class="col-lg-8 fv-row">
                        <input
                            type="text"
                            name="password"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('password') }}"
                        />
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary">Создать</button>
            </div>
        </form>
    </div>
</div>
