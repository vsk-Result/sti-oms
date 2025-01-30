<button id="kt_explore_toggle_ao" class="explore-toggle btn btn-sm bg-body btn-color-gray-700 btn-active-primary shadow-sm position-fixed px-5 fw-bolder zindex-2 top-75 mt-17 end-0 transform-90 rounded-top-0" title="Финансовые менеджеры" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover">
    <span id="kt_explore_toggle_label">Финансовые менеджеры</span>
</button>
<div id="kt_explore" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="explore" data-kt-drawer-activate="true" data-kt-drawer-overlay="false" data-kt-drawer-width="{default: {{ auth()->id() === 12 ? '200px' : '1000px' }}, 'lg': {{ auth()->id() === 12 ? '200px' : '1000px' }}}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_explore_toggle_ao" data-kt-drawer-close="#kt_explore_close">
    <div class="card shadow-none rounded-0 w-100">
        <div class="card-header" id="kt_explore_header">
            <h3 class="card-title fw-bolder text-gray-700">
                Финансовые менеджеры
{{--                <div>--}}
{{--                    <a href="/storage/public/objects-debts-manuals/managers_objects.xlsx" class="btn btn-sm btn-light-success ms-4">--}}
{{--                        Скачать в Excel--}}
{{--                    </a>--}}
{{--                </div>--}}
            </h3>
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
        <div class="card-body pt-0" id="kt_explore_body">
            <div id="kt_explore_scroll" class="scroll-y me-n5 pe-5 h-100" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_explore_body" data-kt-scroll-dependencies="#kt_explore_header" data-kt-scroll-offset="5px">
                <div class="mb-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="fw-bold">
                                    <th class="ps-3" style="max-width: 105px;">Код объекта</th>
                                    <th style="max-width: 220px;">Объект</th>
                                    @if (auth()->id() !== 12)
                                        <th style="max-width: 185px;">Фин.менеджер для согласования счетов / договоров</th>
                                        <th style="max-width: 250px;">Почта</th>
                                        <th style="max-width: 250px;">Телефон</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Object\BObject::active()->orderByDesc('code')->get() as $object)
                                    <tr>
                                        <td class="ps-3">{{ $object->code }}</td>
                                        <td>{{ $object->name }}</td>

                                        @if (auth()->id() !== 12)
                                            @php
                                                $managers = $object->responsiblePersons()->whereIn('position_id', App\Models\Object\ResponsiblePersonPosition::getFinanceManagerPositions())->orderBy('fullname')->get();
                                            @endphp
                                            <td>
                                                @foreach($managers as $manager)
                                                    {{ $manager->fullname }}
                                                    <br>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($managers as $manager)
                                                    <a href="mailto:{{ $manager->email }}">{{ $manager->email }}</a>
                                                    <br>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($managers as $manager)
                                                    <a href="tel:{{ str_replace(' ', '', $manager->phone) }}">{{ $manager->phone }}</a>
                                                    <br>
                                                @endforeach
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach

{{--                                @inject('managersObjectsService', 'App\Services\ManagerObjectService')--}}

{{--                                @foreach($managersObjectsService->getManagers() as $manager)--}}
{{--                                    <tr>--}}
{{--                                        <td class="ps-3">{{ $manager['object_code'] }}</td>--}}
{{--                                        <td>{{ $manager['object_name'] }}</td>--}}
{{--                                        @if (auth()->id() !== 12)--}}
{{--                                            <td>--}}
{{--                                                @foreach($manager['names'] as $managerName)--}}
{{--                                                    {{ $managerName }}--}}
{{--                                                    <br>--}}
{{--                                                @endforeach--}}
{{--                                            </td>--}}
{{--                                            <td>--}}
{{--                                                @foreach($manager['emails'] as $email)--}}
{{--                                                    <a href="mailto:{{ $email }}">{{ $email }}</a>--}}
{{--                                                    <br>--}}
{{--                                                @endforeach--}}
{{--                                            </td>--}}
{{--                                            <td>--}}
{{--                                                @foreach($manager['phones'] as $phone)--}}
{{--                                                    <a href="tel:{{ str_replace(' ', '', $phone) }}">{{ $phone }}</a>--}}
{{--                                                    <br>--}}
{{--                                                @endforeach--}}
{{--                                            </td>--}}
{{--                                        @endif--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
