<button id="kt_explore_toggle" class="explore-toggle btn btn-sm bg-body btn-color-gray-700 btn-active-primary shadow-sm position-fixed px-5 fw-bolder zindex-2 top-75 mt-10 end-0 transform-90 rounded-top-0" title="Расшифровка значений" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover">
    <span id="kt_explore_toggle_label">Расшифровка значений</span>
</button>
<div id="kt_explore" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="explore" data-kt-drawer-activate="true" data-kt-drawer-overlay="false" data-kt-drawer-width="{default:'350px', 'lg': '700px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_explore_toggle" data-kt-drawer-close="#kt_explore_close">
    <div class="card shadow-none rounded-0 w-100">
        <div class="card-header" id="kt_explore_header">
            <h3 class="card-title fw-bolder text-gray-700">Расшифровка значений из фин. отчета</h3>
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
                <ul class="list-unstyled">
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Текущее сальдо</div>
                        <div class="col-md-8">Формула: Сумма расходов + сумма приходов из вкладки "Оплаты" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Общие затраты</div>
                        <div class="col-md-8">Данные берутся из таблички "Распределение общих затрат"</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Промежуточный баланс с текущими долгами и общими расходами компании	</div>
                        <div class="col-md-8">Формула: Текущее сальдо + Общие затраты + Сумма аванса к получению + Долг подписанных актов + Долг гарантийного удержания + Долг подрядчикам + Долг за материалы + Долг на зарплаты ИТР + Долг на зарплаты рабочим</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Общая сумма договоров</div>
                        <div class="col-md-8">Сумма столбца "Сумма договора" из вкладки "Договора" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Остаток денег к получ. с учётом ГУ</div>
                        <div class="col-md-8">Формула: Сумма договоров - Сумма полученных авансов - Сумма оплаты по актам - Сумма к оплате по актам</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">ПРОГНОЗ БАЛАНС + NE ZAKRITI DOGOVOR</div>
                        <div class="col-md-8">Формула: Промежуточный баланс с текущими долгами и общими расходами компании + Остаток денег к получ. с учётом ГУ - Сумма аванса к получению</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Сумма аванса к получению</div>
                        <div class="col-md-8">Сумма столбца "Сумма аванса к получению" из вкладки "Договора" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Долг подписанных актов</div>
                        <div class="col-md-8">Сумма столбца "Сумма неоплаченных работ по актам" из вкладки "Договора" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Всего оплачено авансов</div>
                        <div class="col-md-8">Сумма столбца "Сумма получ. аванса" из вкладки "Договора" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Всего оплачено по актам</div>
                        <div class="col-md-8">Сумма столбца "Оплачено по актам" из вкладки "Договора" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Не закрытый аванс</div>
                        <div class="col-md-8">Формула: Всего оплачено авансов - Сумма столбца "Аванс удержан по актам" из вкладки "Договора" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Долг гарантийного удержания</div>
                        <div class="col-md-8">Сумма столбца "Депозит удержан по актам" из вкладки "Договора" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Долг подрядчикам</div>
                        <div class="col-md-8">Сумма столбца "Сумма" таблицы "Долг подрядчикам" из вкладки "Долги" карточки объекта</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Долг за материалы</div>
                        <div class="col-md-8">Сумма столбца "Сумма" таблицы "Долг поставщикам" из вкладки "Долги" карточки объекта -> документооборот</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Долг на зарплаты ИТР</div>
                        <div class="col-md-8">Берется из смежной системы CRM.LOCAL из сводной таблицы по расчету заработной платы ИТР</div>
                        <div class="separator my-4"></div>
                    </li>
                    <li class="row">
                        <div class="col-md-4 fw-bolder">Долг на зарплаты рабочим</div>
                        <div class="col-md-8">Берется из смежной системы CRM.LOCAL из сводной таблицы по расчету заработной платы рабочих</div>
                        <div class="separator my-4"></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
