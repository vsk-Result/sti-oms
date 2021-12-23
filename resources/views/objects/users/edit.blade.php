@extends('layouts.app')

@section('toolbar-title', 'Изменение доступа')
@section('breadcrumbs', Breadcrumbs::render('object_users.edit'))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение доступа к объекту {{ $object->getName() }}</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('objects.users.update', $object) }}" method="POST">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">
                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Пользователи</label>
                                        <select name="user_id[]" class="form-select form-select-solid" data-control="select2" multiple>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $object->users->where('id', $user->id)->first() ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ route('objects.users.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
