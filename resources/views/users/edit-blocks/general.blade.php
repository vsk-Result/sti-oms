<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Основное</h3>
        </div>
    </div>
    <div>
        <form class="form" action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Фотография</label>
                    <div class="col-lg-8">
                        <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url({{ asset('images/blanks/user_avatar_blank.png') }})">
                            <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $user->getPhoto() }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Изменить фото">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                                <input type="hidden" name="avatar_remove" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Отменить фото">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                            @if ($user->photo)
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Удалить фото">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            @endif
                        </div>
                        <div class="form-text">Поддерживаются форматы: <code>png, jpg, jpeg</code></div>
                        @if ($user->photo)
                            <a target="_blank" href="{{ $user->getPhoto() }}">Посмотреть фото</a>
                        @endif
                        @if ($errors->has('photo'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div>{{ implode(' ', $errors->get('photo')) }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">Имя Фамилия</label>
                    <div class="col-lg-8 fv-row">
                        <input
                            type="text"
                            name="name"
                            class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $user->name) }}"
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
                            value="{{ old('email', $user->email) }}"
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
                            value="{{ old('phone', $user->phone) }}"
                        />
                        @if ($errors->has('phone'))
                            <div class="fv-plugins-message-container invalid-feedback">
                                <div>{{ implode(' ', $errors->get('phone')) }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">Пароль</label>
                    <div class="col-lg-8 fv-row">
                        <a href="{{ route('users.passwords.reset.edit', $user) }}" class="btn btn-light btn-active-light-primary">Изменить пароль</a>
                    </div>
                </div>

                @can('edit admin-users')
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Статус</label>
                        <div class="col-lg-8 fv-row">
                            <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                @foreach($statuses as $statusId => $status)
                                    <option value="{{ $statusId }}" {{ $statusId === $user->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-bold fs-6">Подтверждение Email</label>
                        <div class="col-lg-8 fv-row pt-3">
                            @if ($user->email_verified_at)
                                <p class="fw-bold fs-6">
                                    <span class="badge badge-success fw-bolder me-3">Подтвержден</span>
                                    {{ $user->email_verified_at->format('d.m.Y H:i') }}
                                </p>
                                <a href="{{ route('users.email_confirm_reset', $user) }}" class="btn btn-light btn-active-light-primary" >
                                    Сбросить подтверждение
                                </a>
                            @else
                                <p class="fw-bold fs-6">
                                    <span class="badge badge-primary fw-bolder">Не подтвержден</span>
                                </p>
                                <a href="{{ route('users.email_confirm', $user) }}" class="btn btn-light btn-active-light-primary" >
                                    Подтвердить
                                </a>
                            @endif
                        </div>
                    </div>
                @endcan
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </div>
</div>
