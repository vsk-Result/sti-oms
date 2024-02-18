<button id="kt_explore_toggle_ao" class="explore-toggle btn btn-sm bg-body btn-color-gray-700 btn-active-primary shadow-sm position-fixed px-5 fw-bolder zindex-2 top-75 mt-17 end-0 transform-90 rounded-top-0" title="Таблица активных объектов" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover">
    <span id="kt_explore_toggle_label">Таблица активных объектов</span>
</button>
<div id="kt_explore" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="explore" data-kt-drawer-activate="true" data-kt-drawer-overlay="false" data-kt-drawer-width="{default:'600px', 'lg': '600px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_explore_toggle_ao" data-kt-drawer-close="#kt_explore_close">
    <div class="card shadow-none rounded-0 w-100">
        <div class="card-header" id="kt_explore_header">
            <h3 class="card-title fw-bolder text-gray-700">Таблица активных объектов</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_explore_close">
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <div class="card-body" id="kt_explore_body">
            <div id="kt_explore_scroll" class="scroll-y me-n5 pe-5" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_explore_body" data-kt-scroll-dependencies="#kt_explore_header" data-kt-scroll-offset="5px">
                <div class="mb-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="fw-bold">
                                    <th class="ps-3">Код объекта</th>
                                    <th>Название</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(App\Models\Object\BObject::active()->orderBy('code', 'desc')->get() as $object)
                                    <tr>
                                        <td class="ps-3">{{ $object->code }}</td>
                                        <td>{{ $object->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
