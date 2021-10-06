<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <tbody class="text-gray-600 fw-bold">
            @php
                $permissionTypes = ['index' => 'Общее', 'show' => 'Просмотр', 'create' => 'Создать', 'edit' => 'Изменить'];
            @endphp
            @foreach($permissionCategories as $category => $permissions)
                <tr>
                    <td class="text-gray-800">{{ $category }}</td>
                    <td>
                        <div class="d-flex justify-content-end">
                            @foreach($permissionTypes as $type => $typeName)
                                @php
                                    $findPermission = null;
                                @endphp
                                @foreach($permissions as $permission)
                                    @if (substr($permission->name, 0, strpos($permission->name, ' ')) === $type)
                                        @php
                                            $findPermission = $permission;
                                            break;
                                        @endphp
                                    @endif
                                @endforeach

                                @if ($findPermission)
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        @if (
                                            isset($model)
                                            && $model instanceof \App\Models\User
                                            && $model->getPermissionsViaRoles()->where('name', $findPermission->name)->first()
                                        )
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value="{{ $findPermission->id }}"
                                                checked
                                                disabled
                                            />
                                        @else
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                value="{{ $findPermission->id }}"
                                                name="permissions[]"
                                                {{ (isset($model) && $model->hasPermissionTo($findPermission->name)) ? 'checked' : '' }}
                                            />
                                        @endif

                                        <span class="form-check-label">{{ $typeName }}</span>
                                    </label>
                                @else
                                    <label class="form-check form-check-sm form-check-custom form-check-solid me-5 me-lg-20">
                                        <input class="form-check-input" disabled type="checkbox" value="">
                                        <span class="form-check-label">{{ $typeName }}</span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
