{{-- Ошибки проверки формы — тем же всплывающим блоком, что и обычные сообщения.

     Поля подсвечиваются отдельно (x-form.form-input выводит текст под полем),
     здесь — общий список: он нужен, когда ошибочное поле осталось за экраном. --}}
@if($errors->any())
    <div class="flash-message class__alert app_flach_message"
         role="alert"
         aria-live="assertive">
        <span class="flash-message__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 8v5"/><path d="M12 17h.01"/>
            </svg>
        </span>

        <div class="flash-message__body">
            @if($errors->count() === 1)
                {{ $errors->first() }}
            @else
                <ul class="flash-message__list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <button type="button" class="btn-close app_f_message_close"
                aria-label="Закрыть сообщение"></button>
    </div>
@endif
