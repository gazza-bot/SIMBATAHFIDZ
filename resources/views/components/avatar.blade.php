@props([
    'name' => 'User',
    'photo' => null,
    'size' => 'md',
    'status' => null,
    'attributes' => [],
])

<?php
$sizes = [
    'xs' => 'w-6 h-6 text-xs',
    'sm' => 'w-8 h-8 text-sm',
    'md' => 'w-10 h-10 text-base',
    'lg' => 'w-12 h-12 text-lg',
    'xl' => 'w-16 h-16 text-xl',
    '2xl' => 'w-24 h-24 text-2xl',
];

$statusSizes = [
    'xs' => 'w-1.5 h-1.5',
    'sm' => 'w-2 h-2',
    'md' => 'w-2.5 h-2.5',
    'lg' => 'w-3 h-3',
    'xl' => 'w-4 h-4',
    '2xl' => 'w-5 h-5',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$statusSizeClass = $statusSizes[$size] ?? $statusSizes['md'];

$initials = collect(explode(' ', $name))->map(fn($word) => mb_substr($word, 0, 1))->take(2)->implode('');
?>

<div class="relative inline-flex {{ $attributes->get('class', '') }}">
    @if ($photo)
        <img
            src="{{ asset('storage/' . $photo) }}"
            alt="{{ $name }}"
            class="{{ $sizeClass }} rounded-full object-cover ring-2 ring-white"
        />
    @else
        <div
            class="{{ $sizeClass }} rounded-full bg-primary-100 flex items-center justify-center font-medium text-primary-700 ring-2 ring-white select-none"
            aria-label="{{ $name }}"
        >
            {{ strtoupper($initials) }}
        </div>
    @endif

    @if ($status)
        @php
            $statusColors = [
                'online' => 'bg-success-500',
                'offline' => 'bg-neutral-400',
                'busy' => 'bg-error-500',
                'away' => 'bg-warning-500',
            ];
            $statusColor = $statusColors[$status] ?? $statusColors['offline'];
        @endphp
        <span
            class="absolute bottom-0 right-0 {{ $statusSizeClass }} rounded-full {{ $statusColor }} ring-2 ring-white"
            aria-label="{{ ucfirst($status) }}"
        ></span>
    @endif
</div>