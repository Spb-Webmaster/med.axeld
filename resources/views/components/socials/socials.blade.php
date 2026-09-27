@props(['btnClass' => 'social-btn'])

<div {{ $attributes }}>
    <a href="#" class="{{ $btnClass }}" aria-label="Telegram">
        <img src="{{ Storage::url('/images/icons/footer/telegram.svg') }}" alt="Telegram" />
    </a>
    <a href="#" class="{{ $btnClass }}" aria-label="Facebook">
        <img src="{{ Storage::url('/images/icons/footer/facebook.svg') }}" alt="Facebook" />
    </a>
    <a href="#" class="{{ $btnClass }}" aria-label="YouTube">
        <img src="{{ Storage::url('/images/icons/footer/youtube.svg') }}" alt="YouTube" />
    </a>
    <a href="#" class="{{ $btnClass }}" aria-label="Instagram">
        <img src="{{ Storage::url('/images/icons/footer/instagram.svg') }}" alt="Instagram" />
    </a>
</div>
