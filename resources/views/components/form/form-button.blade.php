{{-- Кнопка формы. Все варианты оформления описаны в resources/css/buttons/buttons.scss.

     variant — цветовая роль без префикса: accent (красный призыв к действию),
     danger (необратимое действие), outline, white, ghost, grey.
     Без variant кнопка синяя — это основное действие.

     Пример: <x-form.form-button :block="true">Войти</x-form.form-button>
             <x-form.form-button variant="danger" size="sm">Удалить</x-form.form-button> --}}
@props([
    'class' => '',
    'type' => 'submit',
    'variant' => '',
    'size' => '',
    'block' => false,
])

<button
    class="btn @if ($variant) btn_{{ $variant }} @endif @if ($size) btn_{{ $size }} @endif @if ($block) btn_block @endif {{ $class }}"
    type="{{ $type }}"
>{{ $slot }}</button>
