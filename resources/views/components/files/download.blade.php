@if(!empty($files))
    <div class="file-list">
        @foreach($files as $file)
            <a class="file-list__item"
               href="{{ Storage::url($file['file']) }}"
               target="_blank"
               download>
                <img class="file-list__icon"
                     src="{{ asset('images/icоns/' . $file['icon']) }}"
                     alt="{{ $file['icon'] }}"
                     width="40" height="40">
                <span class="file-list__name">{{ $file['name'] }}</span>
            </a>
        @endforeach
    </div>
@endif
