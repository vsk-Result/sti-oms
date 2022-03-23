<button id="kt_explore_toggle" class="explore-toggle btn btn-sm bg-body btn-color-gray-700 btn-active-primary shadow-sm position-fixed px-5 fw-bolder zindex-2 top-50 mt-10 end-0 transform-90 rounded-top-0" title="Таблица кост кодов" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-trigger="hover">
    <span id="kt_explore_toggle_label">Кост коды</span>
</button>
<div id="kt_explore" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="explore" data-kt-drawer-activate="true" data-kt-drawer-overlay="false" data-kt-drawer-width="{default:'350px', 'lg': '475px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_explore_toggle" data-kt-drawer-close="#kt_explore_close">
    <div class="card shadow-none rounded-0 w-100">
        <div class="card-header" id="kt_explore_header">
            <h3 class="card-title fw-bolder text-gray-700">Кост коды</h3>
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
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                <th class="min-w-120px">Код</th>
                                <th class="min-w-120px">Описание</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            <tr><td>1.1</td><td>Строительные работы</td></tr>
                            <tr><td>1.2</td><td>Керамические работы</td></tr>
                            <tr><td>1.4</td><td>Укладка пола</td></tr>
                            <tr><td>1.5</td><td>Отделочные работы(молярка,штукатурка, гипсо-картонные работы и натяжные потолки</td></tr>
                            <tr><td>1.7</td><td>Двери</td></tr>
                            <tr><td>1.8</td><td>Фасадные работы</td></tr>
                            <tr><td>1.9</td><td>Кровельные работы</td></tr>
                            <tr><td>1.10</td><td>Ограждение</td></tr>
                            <tr><td>1.11</td><td>Мебель</td></tr>
                            <tr><td>1.12</td><td>Бетонные работы</td></tr>
                            <tr><td>1.15</td><td>Разные материалы</td></tr>
                            <tr><td>1.16</td><td>Транспортные услуги (аренда техники)</td></tr>
                            <tr><td>2.1</td><td>Отопление</td></tr>
                            <tr><td>2.2</td><td>Вентиляция</td></tr>
                            <tr><td>2.3</td><td>Климатизация</td></tr>
                            <tr><td>2.4</td><td>ИТП и котельная</td></tr>
                            <tr><td>2.5</td><td>Спринклерная система пожаротушения</td></tr>
                            <tr><td>2.6</td><td>Гидрантная система</td></tr>
                            <tr><td>2.7</td><td>Водопровод и канализация</td></tr>
                            <tr><td>2.9</td><td>Разные материалы</td></tr>
                            <tr><td>3.1</td><td>Изоляция</td></tr>
                            <tr><td>4.1</td><td>Слаботочная система</td></tr>
                            <tr><td>4.2</td><td>Высоковольтное электричесвто</td></tr>
                            <tr><td>5.1</td><td>Автоматика инжинерных систем</td></tr>
                            <tr><td>6.1</td><td>Архитектура и конструкции</td></tr>
                            <tr><td>6.2</td><td>Проектирование машинных инсталяций</td></tr>
                            <tr><td>6.3</td><td>Проектирование электроустановок</td></tr>
                            <tr><td>6.4</td><td>Проектирование автоматических инженерных систем</td></tr>
                            <tr><td>6.5</td><td>Проектирование интерьера (Дизайн проекта)</td></tr>
                            <tr><td>6.7</td><td>Прочие проектные работы</td></tr>
                            <tr><td>7.1</td><td>Остальные налоги</td></tr>
                            <tr><td>7.2</td><td>НДС</td></tr>
                            <tr><td>7.3</td><td>Налог на имущество</td></tr>
                            <tr><td>7.4</td><td>Штрафы различные (авто)</td></tr>
                            <tr><td>7.5</td><td>Командировки, гостиница, питание, транфер, репрезентация</td></tr>
                            <tr><td>7.6</td><td>Топливо</td></tr>
                            <tr><td>7.7</td><td>Расходы HR (HH, регистрация,приглашения, наркология, страхование и др.)</td></tr>
                            <tr><td>7.8</td><td>Отправка документов (почта, DHL)</td></tr>
                            <tr><td>7.9</td><td>Репрезентация (семинары, рекламы, итд)</td></tr>
                            <tr><td>7.10</td><td>Расходы офиса (кантовары, вода, продукты, бытовая химия)</td></tr>
                            <tr><td>7.11</td><td>Аренда офиса</td></tr>
                            <tr><td>7.12</td><td>Аренда склада</td></tr>
                            <tr><td>7.13</td><td>Бухгалтерский учет и аудит</td></tr>
                            <tr><td>7.14</td><td>Банковские затраты (комиссии, переводы, РКО и пр.)</td></tr>
                            <tr><td>7.15</td><td>Трансфер</td></tr>
                            <tr><td>7.17</td><td>Зарплаты</td></tr>
                            <tr><td>7.18</td><td>Премии</td></tr>
                            <tr><td>7.19</td><td>Налоги с зарплаты (НДФЛ, соц.страх.)</td></tr>
                            <tr><td>7.20</td><td>Налог на прибыль</td></tr>
                            <tr><td>7.21</td><td>Транспорт сотрудников (авио/железнодорожный)</td></tr>
                            <tr><td>7.22</td><td>Транспорт сотрудников (от места пребывания до стройки)</td></tr>
                            <tr><td>7.23</td><td>Грузовой и авиатранспорт грузов</td></tr>
                            <tr><td>7.24</td><td>Питание сотрудников</td></tr>
                            <tr><td>7.25</td><td>СРО (Разрешение на работу)</td></tr>
                            <tr><td>7.26</td><td>Аванс, долг</td></tr>
                            <tr><td>7.27</td><td>Консалтинг</td></tr>
                            <tr><td>7.28</td><td>Адвокат</td></tr>
                            <tr><td>7.29</td><td>Расходы на автомобиль (лизинг, ТО, запчасти, страховки)</td></tr>
                            <tr><td>7.30</td><td>Размещение сотрудников</td></tr>
                            <tr><td>7.31</td><td>Услуги связи (телефон, интернет)</td></tr>
                            <tr><td>7.32</td><td>Разное</td></tr>
                            <tr><td>7.33</td><td>Оргтехника (МФУ, компьютерная техника)</td></tr>
                            <tr><td>7.34</td><td>Программное обеспечение</td></tr>
                            <tr><td>7.35</td><td>Спецодежда</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
