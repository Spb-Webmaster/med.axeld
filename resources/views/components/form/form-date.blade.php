{{-- Поле даты в форме: подпись сверху, календарь проекта, текст ошибки под ним.

     Обёртка над x-form.date-picker, приводящая календарь к виду остальных полей
     (x-form.form-input): та же подпись, та же рамка, тот же текст ошибки.
     Подпись здесь обычная, а не «плавающая»: у поля с календарём значение
     показывается всегда, всплывать ей некуда.

     min/max ограничивают выбор — календарь по этим границам сам строит список лет.

     Пример: <x-form.form-date name="birth_date" label="Дата рождения" :max="now()->format('Y-m-d')"/> --}}
@props([
    'name',
    'label' => '',
    'value' => null,
    'min' => null,
    'max' => null,
    'minField' => null,
    'maxField' => null,
    'required' => false,
    'error' => '',
    'hint' => '',
])

@php
    // Ключ ошибки по умолчанию равен имени поля
    $errorKey = $error ?: $name;
    $id = $name . '-' . \Illuminate\Support\Str::random(5);

    // После неудачной отправки возвращаем то, что выбрал пользователь
    $current = old($name, $value);
@endphp

<div class="input-group input-group--date @error($errorKey) _error @enderror">
    @if($label)
        <label class="mz-select__label" for="{{ $id }}">
            {{ $label }}@if($required)<span>*</span>@endif
        </label>
    @endif

    <x-form.date-picker :name="$name" :id="$id" :value="$current"
                        :min="$min" :max="$max"
                        :min-field="$minField" :max-field="$maxField"
                        :required="$required"/>

    @if($hint)
        <span class="input-group__hint">{{ $hint }}</span>
    @endif

    @error($errorKey)
        <span class="input-group__error">{{ $message }}</span>
    @enderror
</div>
