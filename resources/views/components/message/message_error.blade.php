@if ($errors->any())
    <div class="app_flach_message flashMessage__wrap">
        <div class="class__alert flashMessage">
            <div class="message">
                <div class="btn-close  app_f_message_close"></div>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
@endif
