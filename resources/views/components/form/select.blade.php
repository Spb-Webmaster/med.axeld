{{-- Выпадающий список.

     Нативный <select> остаётся в разметке и отправляет значение как обычно;
     внешний вид ему задаёт resources/js/include/select/mz-select.js,
     оформление — resources/css/components/form/mz-select.scss.

     options — массив «значение => подпись». Если подпись передана массивом
     ['label' => '…', 'disabled' => true], пункт выводится неактивным —
     так делают разделители внутри списка.

     Примеры:
       <x-form.select name="city" label="Город" :options="$cities" placeholder="Выберите город"/>
       <x-form.select name="type" :options="['a' => 'Первый', 'b' => 'Второй']" :selected="$type" :required="true"/> --}}
@props([
    'name',
    'id' => null,
    'label' => '',
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => '',
    'hint' => '',
])

@php
    $id = $id ?: $name . '-' . \Illuminate\Support\Str::random(5);

    // Ключ ошибки по умолчанию равен имени поля — как в x-form.form-input
    $errorKey = $error ?: $name;

    // После неудачной отправки возвращаем то, что выбрал пользователь
    $current = old($name, $selected);
@endphp

<div class="input-group input-group--select">
    @if($label)
        <label class="mz-select__label" for="{{ $id }}">
            {{ $label }}@if($required)<span>*</span>@endif
        </label>
    @endif

    <div class="mz-select @error($errorKey) _error @enderror" data-mz-select>
        <select name="{{ $name }}" id="{{ $id }}" @required($required) @disabled($disabled)>
            {{-- Заглушка с пустым значением: пока она выбрана, скрипт не ставит
                 класс has-value и держит текст приглушённым --}}
            @if($placeholder !== null)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $value => $option)
                @php
                    $text = is_array($option) ? ($option['label'] ?? '') : $option;
                    $isDisabled = is_array($option) && ! empty($option['disabled']);
                @endphp

                <option value="{{ $value }}"
                        @disabled($isDisabled)
                        @selected(! $isDisabled && (string) $current === (string) $value)>{{ $text }}</option>
            @endforeach
        </select>
    </div>

    @if($hint)
        <span class="input-group__hint">{{ $hint }}</span>
    @endif

    @error($errorKey)
        <span class="input-group__error">{{ $message }}</span>
    @enderror
</div>
