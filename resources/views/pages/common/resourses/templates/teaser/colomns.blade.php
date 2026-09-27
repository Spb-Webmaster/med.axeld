<div class="useful-page">
    <section class="useful-wrap useful-library">
        @if(!empty($tabs))
            <div class="useful-tabs" aria-label="Категории материалов">
                @foreach($tabs as $tab)
                    <a href="{{ route($tab['route']) }}"
                       class="useful-tab {{ Route::is($tab['route']) ? 'useful-tab--active' : '' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="useful-cards useful-cards--columns">
            @foreach($items as $item)
                <div class="useful-card useful-card--rose default">
                    <span class="useful-card__kicker">{{ ($page->menu_title)??$page->title }}</span>
                    <h3><a class="h3_teaser" href="{{ route($route, $item->slug) }}">{{ $item->title }}</a></h3>
                    {!!  $item->short_desc !!}
                    <a class="useful-card__link teaser" href="{{ route($route, $item->slug) }}">Подробнее</a>
                </div>
            @endforeach
            {{ $items->withQueryString()->links('pagination::default') }}

        </div>
    </section>
</div>
