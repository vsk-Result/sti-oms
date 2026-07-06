<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'ОМС') }}</title>

    <link rel="icon" href="{{ asset('images/favicon/cropped-STI_logo_2020_512x512-32x32.png') }}" sizes="32x32" />
    <link rel="icon" href="{{ asset('images/favicon/cropped-STI_logo_2020_512x512-192x192.png') }}" sizes="192x192" />
    <link rel="apple-touch-icon" href="{{ asset('images/favicon/cropped-STI_logo_2020_512x512-180x180.png') }}" />
    <meta name="msapplication-TileImage" content="{{ asset('images/favicon/cropped-STI_logo_2020_512x512-270x270.png') }}" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/plugins.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled" data-is-dmitry="{{ auth()->user()->email === 'dmitry.samsonov@dttermo.ru' ? 1 : 0 }}">

@include('partials.calculator')

<div class="d-flex flex-column flex-root">

    <!-- Scroll Top Button -->
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <span class="svg-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
                <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
            </svg>
        </span>
    </div>

    <div class="page d-flex flex-row flex-column-fluid">

        @include('headers.main')

        <div id="kt_content_container" class="d-flex flex-column-fluid align-items-stretch container-fluid">

            @include('sidebars.main')

            @if (!auth()->user()->hasRole(['demo']))
                @include('sidebars.managers_objects')
            @endif

            <div class="wrapper d-flex flex-column flex-row-fluid mt-5 mt-lg-10" id="kt_wrapper">
                <div class="content flex-column-fluid" id="kt_content">

                    @include('toolbars.main')

                    @if (!auth()->user()->hasRole(['demo']))
                        @include('sidebars.cost_codes')
                    @endif

                    @yield('content')
                </div>

                @include('footers.main')
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('js/plugins.bundle.js') }}"></script>
<script src="{{ asset('js/scripts.bundle.js') }}"></script>
<script src="{{ asset('vendor/freezeTable/freeze.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('vendor/fslightbox/fslightbox.bundle.js') }}"></script>
@stack('scripts')

<script>
    const menu = [
        {
            title:"Загрузки",
            icon: "ki-chart-simple",
            children: [
                {
                    title:"Загрузки оплат",
                    url:"{{ route('payment_imports.index') }}"
                },
                {
                    title:"Проверка касс CRM",
                    url:"{{ route('crm_cash_check.index') }}"
                }
            ]
        },
        {
            title:"Сводные",
            icon: "ki-profile-circle",
            children: [
                {
                    title:"Распределение общих затрат",
                    url:"{{ route('general_costs.index') }}"
                },
                {
                    title:"Распределение услуг по трансферу",
                    url:"{{ route('distribution_transfer_service.index') }}"
                }
            ]
        },
        {
            title:"Отчеты",
            icon: "ki-profile-circle",
            children: [
                {
                    title:"Свод отчетов",
                    url:"{{ route('reports.all_reports.index') }}"
                },
                {
                    title:"Финансовый отчет",
                    url:"{{ route('finance_report.index') }}"
                },
                {
                    title:"Отчет по общим затратам",
                    url:"{{ route('general_report.index') }}"
                },
                {
                    title:"Отчет CASH FLOW",
                    url:"{{ route('pivots.cash_flow.index') }}"
                },
                {
                    title:"Отчет по категориям",
                    url:"{{ route('pivots.acts_category.index') }}"
                },
                {
                    title:"Отчет по балансам",
                    url:"{{ route('pivots.balances.index') }}"
                },
                {
                    title:"Отчет о движении денежных средств",
                    url:"{{ route('pivots.money_movement.index') }}"
                },
                {
                    title:"Отчет о проживании",
                    url:"{{ route('pivots.residence.index') }}"
                },
                {
                    title:"Расчет стоимости рабочих",
                    url:"{{ route('pivots.calculate_workers_cost.index') }}"
                }
            ]
        },
        {
            title:"Схемы",
            icon: "ki-profile-circle",
            children: [
                {
                    title:"Схема взаимодействия",
                    url:"{{ route('schemas.interactions.index') }}"
                }
            ]
        },
        {
            title:"Реестры",
            icon: "ki-profile-circle",
            children: [
                {
                    title:"Объекты",
                    url:"{{ route('objects.index') }}"
                },
                {
                    title:"Оплаты",
                    url:"{{ route('payments.index') }}"
                },
                {
                    title:"История оплат",
                    url:"{{ route('payments.history.index') }}"
                },
                {
                    title:"Списания",
                    url:"{{ route('writeoffs.index') }}"
                },
                {
                    title:"План расходов к оплате",
                    url:"{{ route('tax_plan.index', ['filter' => 'current']) }}"
                },
                {
                    title:"Кассы",
                    url:"{{ route('cash_accounts.index') }}"
                },
                {
                    title:"Наши компании",
                    url:"{{ route('companies.index') }}"
                },
                {
                    title:"Контрагенты",
                    url:"{{ route('organizations.index') }}"
                },
                {
                    title:"Начисленные налоги",
                    url:"{{ route('accrued_taxes.index') }}"
                }
            ]
        },
        {
            title:"Долги",
            icon: "ki-profile-circle",
            children: [
                {
                    title:"От СТИ",
                    url:"{{ route('pivots.debts.index') }}"
                },
                {
                    title:"К СТИ",
                    url:"{{ route('pivots.acts.index') }}"
                },
                {
                    title:"ДТ - СТИ",
                    url:"{{ route('pivots.dtsti.index') }}"
                },
                {
                    title:"По контрагентам",
                    url:"{{ route('pivots.organization_debts.index') }}"
                },
                {
                    title:"Займы / Кредиты",
                    url:"{{ route('loans.index') }}"
                }
            ]
        },
        {
            title:"Документооборот",
            icon: "ki-profile-circle",
            children: [
                {
                    title:"Договора",
                    url:"{{ route('contracts.index') }}"
                },
                {
                    title:"Акты",
                    url:"{{ route('acts.index') }}"
                },
                {
                    title:"Банковские гарантии",
                    url:"{{ route('bank_guarantees.index') }}"
                },
                {
                    title:"Депозиты",
                    url:"{{ route('deposits.index') }}"
                },
                {
                    title:"Гарантийные удержания",
                    url:"{{ route('guarantees.index') }}"
                }
            ]
        },
        {
            title:"Другое",
            icon: "ki-profile-circle",
            children: [
                {
                    title:"Статус касс",
                    url:"{{ route('crm_costs.index') }}"
                },
                {
                    title:"Статус переноса оплат на карты из CRM",
                    url:"{{ route('crm.avanses.imports.split.index') }}"
                },
                {
                    title:"Планировщик задач",
                    url:"{{ route('scheduler.index') }}"
                },
                {
                    title:"Статус загруженных файлов по долгам объектов",
                    url:"{{ route('upload_debts_status.index') }}"
                },
                {
                    title:"Разбивка налогов",
                    url:"{{ route('tax_split.index') }}"
                }
            ]
        },
    ];

    element = document.querySelector("#kt_docs_search_handler_menu");

    if (element) {
        resultsElement = element.querySelector('[data-kt-search-element="results"]');
        emptyElement = element.querySelector('[data-kt-search-element="empty"]');

        searchObject = new KTSearch(element);

        searchObject.on("kt.search.process", (s) => {
            s.complete();
        });

        searchObject.on("kt.search.clear", () => {
            resultsElement.classList.add("d-none");
            emptyElement.classList.add("d-none");
        });
    }

    renderRecent();

    const searchIndex = [];

    buildSearchIndex(menu);

    function buildSearchIndex(items, path = [], root = null) {

        items.forEach(item => {

            const currentRoot = root || item;
            const currentPath = [...path, item.title];

            if (item.url) {

                searchIndex.push({
                    title: item.title,
                    url: item.url,
                    path: currentPath.join(" / "),
                    group: currentRoot.title,
                    groupIcon: currentRoot.icon || "ki-folder"
                });

            }

            if (item.children) {

                buildSearchIndex(
                    item.children,
                    currentPath,
                    currentRoot
                );

            }

        });

    }

    function fuzzyMatch(text, query) {
        text = text.toLowerCase();
        query = query.toLowerCase();

        let qi = 0;
        for (let i = 0; i < text.length && qi < query.length; i++) {
            if (text[i] === query[qi]) {
                qi++;
            }
        }

        return qi === query.length;
    }

    function highlight(text, query) {
        if (!query) {
            return text;
        }

        const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        return text.replace(
            new RegExp("(" + escaped + ")", "ig"),
            "<mark>$1</mark>"
        );
    }

    function score(item, query) {
        const q = query.toLowerCase();
        const title = item.title.toLowerCase();
        const path = item.path.toLowerCase();

        if (title === q)
            return 1000;
        if (title.startsWith(q))
            return 900;
        if (title.includes(q))
            return 700;
        if (path.includes(q))
            return 500;
        if (fuzzyMatch(path, q))
            return 300;

        return 0;
    }

    function abbreviation(text) {

        return text
            .toLowerCase()
            .split(/[\s\/-]+/)
            .map(word => {

                let result = "";

                for (const ch of word) {

                    if ("аеёиоуыэюя".includes(ch))
                        continue;

                    result += ch;
                }

                return result;

            })
            .join("");

    }

    function searchQuery(query) {

        query = query.toLowerCase().trim();

        if (!query)
            return [];

        let result = [];

        // 1. title
        result = searchIndex.filter(item =>
            item.title.toLowerCase().includes(query)
        );

        // 2. path
        if (!result.length) {

            result = searchIndex.filter(item =>
                item.path.toLowerCase().includes(query)
            );

        }

        // 3. abbreviation
        if (!result.length) {

            result = searchIndex.filter(item =>
                abbreviation(item.title).startsWith(query)
            );

        }

        return result.sort((a, b) => score(b, query) - score(a, query));

    }

    function saveRecent(item){
        let recent = getRecent();
        recent = recent.filter(x=>x.url!==item.url);
        recent.unshift(item);
        recent = recent.slice(0,8);

        localStorage.setItem(
            "recentMenu",
            JSON.stringify(recent)
        );
    }

    function getRecent(){
        return JSON.parse(
            localStorage.getItem("recentMenu") || "[]"
        );
    }

    function renderResults(list, query) {
        const results = $('[data-kt-search-element="results"]');

        results.empty();

        if (list.length === 0) {
            $('[data-kt-search-element="results"]').addClass("d-none");
            $('[data-kt-search-element="empty"]').removeClass("d-none");

            return;
        }

        $('[data-kt-search-element="empty"]').addClass("d-none");
        $('[data-kt-search-element="results"]').removeClass("d-none");

        const groups = {};

        list.forEach(item => {
            if (!groups[item.group]) {
                groups[item.group] = [];
            }

            groups[item.group].push(item);
        });

        Object.keys(groups).forEach(group => {
            const first = groups[group][0];

            results.append(`
                <div class="pt-2 pb-2">
                    <div class="d-flex align-items-center">
                        <i class="ki-duotone ${first.groupIcon} fs-4 text-primary me-2"></i>
                        <span class="fw-bolder text-gray-700">
                            ${group}
                        </span>
                    </div>
                </div>
            `);

            groups[group].forEach(item => {
                results.append(renderItem(item, query));
            });
        });
    }

    function renderRecent() {
        renderResults(getRecent(), "");
    }

    const input = $('[data-kt-search-element="input"]');

    input.on("input",function() {
        const value=$(this).val().trim();
        if(value===""){
            renderRecent();
            return;
        }
        renderResults(
            searchQuery(value),
            value
        );
    });

    $(document).on("click",".search-item",function(){
        const url=$(this).data("url");
        const item=searchIndex.find(x=>x.url===url);
        saveRecent(item);
    });

    function renderItem(item, query) {
        return `
            <a href="${item.url}" class="search-item d-flex align-items-center px-3 py-3 rounded mb-1" data-url="${item.url}">
<!--                <div class="symbol symbol-35px me-3">-->
<!--                    <span class="symbol-label bg-light-primary">-->
<!--                        <i class="ki-duotone ki-folder fs-4 text-primary"></i>-->
<!--                    </span>-->
<!--                </div>-->

                <div class="flex-grow-1">
                    <div class="fw-semibold">
                        ${highlight(item.title, query)}
                    </div>
                    <div class="text-muted fs-7">
                        ${highlight(item.path, query)}
                    </div>
                </div>
            </a>
        `;
    }
</script>
</body>
</html>
