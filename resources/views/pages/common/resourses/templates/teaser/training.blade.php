<div class="useful-page">
    <section class="useful-wrap useful-library">
        @if(!empty($categories) && $categories->isNotEmpty())
            <div class="useful-tabs" aria-label="Категории обучения">
                @foreach($categories as $category)
                    <span class="useful-tab">{{ $category->title }}</span>
                @endforeach
            </div>
        @endif

        <div class="useful-cards useful-cards--columns">
            @foreach($items as $item)
                <div class="useful-card useful-card--rose default">
                    <span class="useful-card__kicker">{{ $item->categories->first()?->title ?? ($page->menu_title ?? $page->title) }}</span>
                    <h3><a class="h3_teaser" href="{{ route($route, $item->slug) }}">{{ $item->title }}</a></h3>
                    {!!  $item->short_desc !!}
                    <a class="useful-card__link teaser" href="{{ route($route, $item->slug) }}">Подробнее</a>
                </div>
            @endforeach
            {{ $items->withQueryString()->links('pagination::default') }}

        </div>
    </section>
</div>

