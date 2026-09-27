{{-- Блок «Вопрос — ответ».

     Данные приходят из Json-поля `faq` (см. NewsFormPage, HomePage, NewsPage):
     массив блоков вида ['title' => '…', 'options' => [['question' => '…', 'answer' => '…'], …]].

     Стили — resources/css/components/modules/faq.scss,
     раскрытие — resources/js/include/site/faq.js (faqAccordion).

     Пример: <x-modules.faq :items="$item->faq"/> --}}
@props([
    'items' => [],
])

@php
    /*
     | Json-поле в админке почти всегда содержит мусор: блок добавили и не заполнили,
     | пару добавили и оставили пустой. Без чистки такие записи доезжают до вёрстки
     | пустым <details> — браузер рисует его своим заголовком «Сведения» — и пустой
     | секцией с отступами.
     |
     | Поэтому: пара живёт, только если заполнен вопрос (без него у аккордеона нет
     | заголовка), блок — если в нём осталась хоть одна пара, секция — если остался
     | хоть один блок.
     */
    $faqBlocks = collect($items)
        ->map(fn ($block) => [
            'title'   => trim((string) data_get($block, 'title', '')),
            'options' => collect(data_get($block, 'options', []))
                ->filter(fn ($qa) => trim((string) data_get($qa, 'question', '')) !== '')
                ->values(),
        ])
        ->filter(fn ($block) => $block['options']->isNotEmpty())
        ->values();
@endphp

@if($faqBlocks->isNotEmpty())
    <section class="faq" id="faq">
        <div class="container faq__content">

            @foreach($faqBlocks as $block)
                @if($block['title'] !== '')
                    <h2>{{ $block['title'] }}</h2>
                @endif

                <div class="faq-list">
                    @foreach($block['options'] as $index => $qa)
                        {{-- Первый вопрос открыт: иначе блок выглядит пустым списком заголовков --}}
                        <details {{ $index === 0 ? 'open' : '' }}>
                            <summary>{{ data_get($qa, 'question') }}</summary>

                            {{-- Ответ приходит из редактора вместе с <p>, поэтому обёртка —
                                 div: вкладывать абзацы в абзац нельзя. Раскрытием управляет
                                 faq.js, он ищет именно .faq-list__answer --}}
                            <div class="faq-list__answer">{!! data_get($qa, 'answer') !!}</div>
                        </details>
                    @endforeach
                </div>
            @endforeach

        </div>
    </section>
@endif
