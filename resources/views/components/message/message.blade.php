{{-- Всплывающее сообщение после действия (сохранили профиль, сменили пароль).

     Текст кладёт в сессию хелпер flash()->info() / flash()->alert(),
     класс приходит из config/flash.php: class__info или class__alert.

     Стили — resources/css/message/flach_message.scss,
     закрытие и автоскрытие — resources/js/include/flash_message/flash_message.js --}}
@if($message = flash()->get())
    @php($isAlert = $message->class() === 'class__alert')

    <div class="flash-message {{ $message->class() }} app_flach_message"
         role="status"
         aria-live="polite">
        <span class="flash-message__icon" aria-hidden="true">
            @if($isAlert)
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 8v5"/><path d="M12 17h.01"/>
                </svg>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 12.5l5.5 5.5L20 7"/>
                </svg>
            @endif
        </span>

        <div class="flash-message__body">{!! $message->message() !!}</div>

        <button type="button" class="btn-close app_f_message_close"
                aria-label="Закрыть сообщение"></button>
    </div>
@endif
