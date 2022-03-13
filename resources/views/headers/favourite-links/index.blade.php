<div data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion me-lg-1">
    <span class="menu-link py-3">
        <span class="menu-title">Быстрый переход</span>
        <span class="menu-arrow d-lg-none"></span>
    </span>

    <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-rounded-0 py-lg-4 w-lg-225px">
        <div class="menu-item">
            <a class="menu-link py-3" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#createFavouriteLinkModal">
                <span class="menu-title text-muted">Добавить текущую ссылку</span>
            </a>
        </div>

        <div class="separator opacity-75"></div>

        @can('index payments')
            <div class="menu-item">
                <a class="menu-link py-3" href="{{ route('payments.index') }}?count_per_page=30&object_id%5B%5D=Трансфер">
                    <span class="menu-title">Трансферы</span>
                </a>
            </div>

            <div class="menu-item">
                <a class="menu-link py-3" href="{{ route('payments.index') }}?count_per_page=30&object_id%5B%5D=Общее">
                    <span class="menu-title">Общие затраты</span>
                </a>
            </div>

            <div class="separator opacity-75"></div>
        @endcan

        @foreach(auth()->user()->favouriteLinks as $link)
            <div class="menu-item">
                <a class="menu-link py-3" href="{{ $link->link }}">
                    <span class="menu-title">{{ $link->name }}</span>
                </a>
            </div>
        @endforeach
    </div>
</div>
