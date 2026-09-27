@if($message = flash()->get())
    <div class="app_flach_message flashMessage__wrap">
    <div class="{{ $message->class() }} flashMessage">
        <div class="message">
            <div class="btn-close app_f_message_close"></div>
        {!! $message->message() !!}
        </div>
    </div>
    </div>
@endif



