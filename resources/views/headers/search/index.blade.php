
<div class="menu-item menu-lg-down-accordion me-lg-1">
    <div id="kt_docs_search_handler_menu"
         class="d-flex align-items-stretch"

         data-kt-search-keypress="true"
         data-kt-search-enter="true"
         data-kt-search-layout="menu"

         data-kt-menu-trigger="auto"
         data-kt-menu-overflow="false"
         data-kt-menu-permanent="true"
         data-kt-menu-placement="bottom-start">

        <div class="d-flex align-items-center" data-kt-search-element="toggle" id="kt_header_search_toggle">
            <span class="menu-link py-3">
                <a class="menu-title position-relative" href="javascript:void(0)" style="font-weight: 600; font-size: 1.15rem;">
                    <i class="fa fa-search me-3" style="color: #3f4254;"></i> Поиск
                </a>
            </span>
        </div>

        <div data-kt-search-element="content" class="menu menu-sub menu-sub-dropdown p-7 w-325px w-md-375px">
            <div data-kt-search-element="wrapper">
                <form data-kt-search-element="form" class="w-100 position-relative mb-3" autocomplete="off">
                    <input type="text"
                           class="form-control form-control-flush"
                           name="search"
                           value=""
                           placeholder="Поиск по разделам..."
                           data-kt-search-element="input" />

                    <span class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-1" data-kt-search-element="spinner">
                        <span class="spinner-border h-15px w-15px align-middle text-gray-500"></span>
                    </span>

                    <span class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none" data-kt-search-element="clear"></span>
                </form>

                <div class="separator border-gray-200 mb-6"></div>

                <div data-kt-search-element="results" class="scroll-y mh-200px mh-lg-300px my-2 me-n2 pe-2"></div>

                <div data-kt-search-element="empty" class="text-center d-none">
                    Результатов нет
                </div>
            </div>
        </div>
    </div>
</div>