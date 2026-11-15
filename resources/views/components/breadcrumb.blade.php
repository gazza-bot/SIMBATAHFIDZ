@props([
    'items' => [],
    'separator' => 'chevron',
    'attributes' => [],
])

<?php
$separators = [
    'chevron' => '<svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
    'slash' => '<span class="text-neutral-400">/</span>',
    'arrow' => '<svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5l7 7-7 7"/></svg>',
];
$separatorHtml = $separators[$separator] ?? $separators['chevron'];
?>

<nav class="flex items-center gap-1.5" aria-label="Breadcrumb" {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}>
    <ol class="flex items-center gap-1.5">
        @foreach ($items as $index => $item)
            @php
                $isLast = $index === array_key_last($items);
                $isFirst = $index === 0;
            @endphp

            @if (!$isFirst)
                <li class="flex items-center" aria-hidden="true">
                    {!! $separatorHtml !!}
                </li>
            @endif

            <li>
                @if ($isLast)
                    <span class="text-sm font-medium text-neutral-900" aria-current="page">{{ $item['label'] ?? $item }}</span>
                @elseif (isset($item['url']))
                    <a href="{{ $item['url'] }}" class="text-sm text-neutral-500 hover:text-primary-600 transition-colors">{{ $item['label'] ?? $item }}</a>
                @else
                    <span class="text-sm text-neutral-500">{{ $item['label'] ?? $item }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>