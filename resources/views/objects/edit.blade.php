@extends('layouts.app')

@section('title', 'Изменение объекта ' . $object->code)
@section('toolbar-title', 'Изменение объекта')
@section('breadcrumbs', Breadcrumbs::render('objects.edit', $object))

@section('content')
    <div class="post" id="kt_post">
        <div id="kt_content_container" class="container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Изменение объекта</span>
                    </h3>
                </div>
                <div class="card-body py-3">
                    <form class="form" action="{{ route('objects.update', $object) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-5">
                            <div class="col-md-12 fv-row">
                                <div class="mb-7 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Код</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="code"
                                                value="{{ old('code', $object->code) }}"
                                                required
                                                autofocus
                                            />
                                        </div>
                                        @if ($errors->has('code'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('code')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-check form-check-custom form-check-solid mb-6 fw-bold fs-6">
                                    <input name="is_without_worktype" class="form-check-input" type="checkbox" value="" id="flexCheckChecked" {{ $object->is_without_worktype ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexCheckChecked">Без вида работ</label>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Название</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="form-control form-control-lg form-control-solid {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                type="text"
                                                name="name"
                                                value="{{ old('name', $object->name) }}"
                                                required
                                            />
                                        </div>
                                        @if ($errors->has('name'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('name')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="form-label fw-bolder text-dark fs-6">Адрес</label>
                                    <input
                                        class="form-control form-control-lg form-control-solid {{ $errors->has('address') ? 'is-invalid' : '' }}"
                                        type="text"
                                        value="{{ old('address', $object->address) }}"
                                        name="address"
                                    />
                                    @if ($errors->has('address'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div>{{ implode(' ', $errors->get('address')) }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="form-label fw-bolder text-dark fs-6">Дата закрытия</label>
                                    <input
                                        class="date-range-picker-single form-control form-control-lg form-control-solid {{ $errors->has('closing_date') ? 'is-invalid' : '' }}"
                                        type="text"
                                        value="{{ old('closing_date', $object->closing_date) }}"
                                        name="closing_date"
                                        readonly
                                    />
                                    @if ($errors->has('closing_date'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div>{{ implode(' ', $errors->get('closing_date')) }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="fv-row mb-10">
                                    <label class="form-label fw-bolder text-dark fs-6">Заказчики</label>
                                    <select name="customer_id[]" class="form-select form-select-solid" data-control="select2" multiple>
                                        @foreach($organizations as $organization)
                                            <option value="{{ $organization->id }}" {{ $object->customers->where('id', $organization->id)->first() ? 'selected' : '' }}>{{ $organization->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-10 fv-row">
                                    <div class="mb-1">
                                        <label class="form-label fw-bolder text-dark fs-6">Сумма свободного лимита АВ к получению</label>
                                        <div class="position-relative mb-3">
                                            <input
                                                class="amount-mask form-control form-control-lg form-control-solid"
                                                type="text"
                                                name="free_limit_amount"
                                                value="{{ old('free_limit_amount', $object->free_limit_amount) }}"
                                            />
                                        </div>
                                    </div>
                                </div>


                                <div class="fv-row mb-10">
                                    <label class="col-lg-4 col-form-label fw-bold fs-6">Фотография</label>
                                    <div class="col-lg-8">
                                        <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url({{ asset('images/blanks/object_photo_blank.jpg') }})">
                                            <div class="image-input-wrapper w-125px h-125px" style="background-image: url({{ $object->getPhoto() }})"></div>
                                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Изменить фото">
                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="avatar_remove" />
                                            </label>
                                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Отменить фото">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                                            @if ($object->photo)
                                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Удалить фото">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                                            @endif
                                        </div>
                                        <div class="form-text">Поддерживаются форматы: <code>png, jpg, jpeg</code></div>
                                        @if ($errors->has('photo'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div>{{ implode(' ', $errors->get('photo')) }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-10 fv-row">
                                    <label class="form-label fw-bolder text-dark fs-6">Статус</label>
                                    <div class="position-relative mb-3">
                                        <select name="status_id" data-control="select2" class="form-select form-select-solid form-select-lg">
                                            @foreach($statuses as $statusId => $status)
                                                <option value="{{ $statusId }}" {{ $statusId === $object->status_id ? 'selected' : '' }}>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <h3 class="mb-10 mt-6">Ответственные лица</h3>

                                    <div id="responsible-person-template" class="col-md-3 mb-10 fv-row" style="display: none;">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input
                                                            class="form-control form-control-lg form-control-solid"
                                                            type="text"
                                                            value=""
                                                            name="person_name[]"
                                                    />
                                                </td>
                                                <td>
                                                    <select name="person_position[]" class="form-select form-select-solid">
                                                        @foreach($positions as $position)
                                                            <option value="{{ $position['id'] }}">{{ $position['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                            class="form-control form-control-lg form-control-solid"
                                                            type="email"
                                                            value=""
                                                            name="person_email[]"
                                                    />
                                                </td>
                                                <td>
                                                    <input
                                                            class="form-control form-control-lg form-control-solid"
                                                            type="text"
                                                            value=""
                                                            name="person_phone[]"
                                                    />
                                                </td>
                                                <td>
                                                    <button
                                                            type="button"
                                                            class="destroy-person btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
                                                    >
                                                        Удалить
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mb-4 d-flex flex-left">
                                        <button
                                            type="button"
                                            id="create-responsible-person"
                                            class="mt-4 btn btn-outline btn-outline-dashed btn-outline-success btn-active-light-success me-2 mb-2"
                                        >
                                            Добавить
                                        </button>
                                    </div>

                                    <div class="d-flex flex-left mb-4">
                                        <table id="responsible-persons-table" class="table align-middle table-row-dashed fs-6">
                                            <thead>
                                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                <th class="min-w-150px">ФИО</th>
                                                <th class="min-w-150px">Должность</th>
                                                <th class="min-w-150px">Email</th>
                                                <th class="min-w-150px">Телефон</th>
                                                <th class="min-w-150px rounded-end pe-4">Действие</th>
                                            </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-bold">
                                                @foreach($object->responsiblePersons as $person)
                                                    <tr>
                                                        <td>
                                                            <input
                                                                class="form-control form-control-lg form-control-solid"
                                                                type="text"
                                                                value="{{ $person->fullname }}"
                                                                name="isset_person_name[{{ $person->id }}]"
                                                                required
                                                            />
                                                        </td>
                                                        <td>
                                                            <select name="isset_person_position[{{ $person->id }}]" class="form-select form-select-solid" data-control="select2">
                                                                @foreach($positions as $position)
                                                                    <option value="{{ $position['id'] }}" {{ $position['id'] === $person->position_id ? 'selected' : '' }}>{{ $position['name'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input
                                                                class="form-control form-control-lg form-control-solid"
                                                                type="email"
                                                                value="{{ $person->email }}"
                                                                name="isset_person_email[{{ $person->id }}]"
                                                            />
                                                        </td>
                                                        <td>
                                                            <input
                                                                    class="form-control form-control-lg form-control-solid"
                                                                    type="text"
                                                                    value="{{ $person->phone }}"
                                                                    name="isset_person_phone[{{ $person->id }}]"
                                                            />
                                                        </td>
                                                        <td>
                                                            <button
                                                                    type="button"
                                                                    class="destroy-person btn btn-outline btn-outline-dashed btn-outline-danger btn-active-light-danger"
                                                            >
                                                                Удалить
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <h3 class="mb-10 mt-6">Прогнозируемые затраты</h3>

                                    <div class="alert alert-dismissible bg-light-primary d-flex flex-column flex-sm-row p-5 mb-10">
                                        <i class="ki-duotone ki-search-list fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        <div class="d-flex flex-column pe-0 pe-sm-10">
                                            <h5 class="mb-1">Заполнение прогнозируемых затрат</h5>
                                            <span>Если задать поле пустым, расчет будет произведен автоматически, иначе будет браться значение из поля, даже если это 0</span>
                                        </div>
                                    </div>

                                    @foreach($prognozFields as $name => $field)
                                        @php
                                            $planPayment = $object->planPayments->where('field', $field)->first();
                                        @endphp
                                        <div class="pt-4 mb-10 fv-row">
                                            <label class="form-label fw-bolder text-dark fs-6">{{ $name }}</label>
                                            <input
                                                class="amount-mask form-control form-control-lg form-control-solid"
                                                type="{{ $field === 'prognoz_material' ? 'hidden' : 'text' }}"
                                                value="{{ old($field, $planPayment->isAutoCalculation() ? null : $planPayment->amount) }}"
                                                name="{{ $field }}"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-center py-3">
                            <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary me-3">
                                <span class="indicator-label">Сохранить</span>
                            </button>
                            <a href="{{ route('objects.index') }}" class="btn btn-light">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            $('#create-responsible-person').on('click', function () {
                const $person = $('#responsible-person-template').clone();
                $person.find('select').attr('data-control', 'select2');
                $('#responsible-persons-table tbody').append($person.find('tr'));

                KTApp.initSelect2();
            });

            $(document).on('click', '.destroy-person', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
