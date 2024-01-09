<button id="kt_explore_toggle" class="explore-toggle btn btn-sm bg-body btn-color-gray-700 btn-active-primary shadow-sm position-fixed px-5 fw-bolder zindex-2 top-50 mt-10 end-0 transform-90 rounded-top-0" title="Таблица Статья затратов" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover">
    <span id="kt_explore_toggle_label">Статьи затрат компании СТИ</span>
</button>
<div id="kt_explore" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="explore" data-kt-drawer-activate="true" data-kt-drawer-overlay="false" data-kt-drawer-width="{default:'600px', 'lg': '600px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_explore_toggle" data-kt-drawer-close="#kt_explore_close">
    <div class="card shadow-none rounded-0 w-100">
        <div class="card-header" id="kt_explore_header">
            <h3 class="card-title fw-bolder text-gray-700">Статьи затрат компании СТИ</h3>
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
                                <tr>
                                    <th class="ps-3">Код затрат</th>
                                    <th>Заголовок статьи затрат</th>
                                    <th>Расшифровка перечня расходов</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(App\Models\KostCode::getCodes() as $codeL1)
                                    <tr class="border-bottom-dashed border-bottom-1 border-gray-300 my-3 fw-boldest fs-5" style="background-color: #f05a22bf">
                                        <td class="ps-3">{{ $codeL1['code'] }}</td>
                                        <td>{{ $codeL1['title'] }}</td>
                                        <td>{{ $codeL1['description'] }}</td>
                                    </tr>
                                    @if (count($codeL1['children']) > 0)
                                        @foreach($codeL1['children'] as $codeL2)
                                            <tr class="border-bottom-dashed border-bottom-1 border-gray-300 my-3 fw-bolder fs-6" style="background-color: #f05a2230">
                                                <td class="ps-3">{{ $codeL2['code'] }}</td>
                                                <td>{{ $codeL2['title'] }}</td>
                                                <td>{{ $codeL2['description'] }}</td>
                                            </tr>
                                            @if (count($codeL2['children']) > 0)
                                                @foreach($codeL2['children'] as $codeL3)
                                                    <tr class="border-bottom-dashed border-bottom-1 border-gray-300 my-3 fs-7">
                                                        <td class="ps-3">{{ $codeL3['code'] }}</td>
                                                        <td>{{ $codeL3['title'] }}</td>
                                                        <td>{{ $codeL3['description'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
