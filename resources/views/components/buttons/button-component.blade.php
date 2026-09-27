@props([
    'id' => '',
    'target' => false,
    'tag' => 'a',
    'type' => 'button',
    'data' => ''
])
@if($tag == 'a')
    <a href="{{ $href }}"  {{ $attributes->class([]) }} id="{{ $id }}" @if($target)  target="_blank" @endif  {{ $data }}>
        {{ $slot }}
    </a>
@else
<button type="{{ $type }}" {{ $attributes->class([]) }} id="{{ $id }}">
    {{ $slot }}
</button>
@endif
