{{-- Поле формы с «плавающей» подписью.

     Порядок input → label обязателен: оформление (.input-group в
     resources/css/components/form/form-input.scss) поднимает подпись селектором
     `.input-group__input:focus + label`, соседним, а не родительским.
     По этой же причине у поля всегда есть placeholder — хотя бы из пробела:
     без него `:not(:placeholder-shown)` не сработает и подпись ляжет на текст.

     Пример: <x-form.form-input name="email" type="email" label="Email" :required="true"/> --}}
@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'class' => '',
    'placeholder' => ' ',
    'autocomplete' => 'off',
    'value' => '',
    'autofocus' => false,
    'required' => false,
    'error' => '',
    'hint' => '',
])

@php
    // Ключ ошибки по умолчанию равен имени поля
    $errorKey = $error ?: $name;
    $id = $name . '-' . \Illuminate\Support\Str::random(5);
@endphp

{{-- У type="date" браузер не показывает placeholder, поэтому `:placeholder-shown`
     не сработает — подпись такому полю поднимаем сразу, модификатором _date --}}
<div class="input-group @if ($type === 'date') _date @endif">
    {{-- type="tel" получает класс .imask — маску вешает resources/js/include/imask.js --}}
    <input
        class="input-group__input {{ $class }} {{ $type === 'tel' ? 'imask' : '' }} @error($errorKey) _error @enderror"
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        autocomplete="{{ $autocomplete }}"
        @required($required)
        @if ($autofocus) autofocus @endif
    >

    @if ($label)
        <label class="input-group__label" for="{{ $id }}">
            {{ $label }}@if ($required)<span>*</span>@endif
        </label>
    @endif

    @if ($hint)
        <span class="input-group__hint">{{ $hint }}</span>
    @endif

    @error($errorKey)
        <span class="input-group__error">{{ $message }}</span>
    @enderror
</div>
