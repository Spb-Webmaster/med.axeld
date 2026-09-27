<!-- Навигация -->
<div class="top_menu">
    <div class="header-nav-bar">
        <div class="header-inner">
            <nav class="header-nav" aria-label="Основная навигация">
                @foreach($navItems as $item)
                    <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                       class="header-nav-link {{ request()->routeIs($item['pattern']) ? 'header-nav-link--active' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <x-cabinet.enter />
        </div>
    </div>
</div>
