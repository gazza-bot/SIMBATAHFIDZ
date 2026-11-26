@props([
    'id' => null,
    'open' => false,
    'title' => null,
    'description' => null,
    'size' => 'md',
    'closeable' => true,
    'onClose' => null,
])

@php
$sizes = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    'full' => 'max-w-4xl',
    'fullscreen' => 'max-w-full w-full m-0 rounded-none h-full',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];
$closeHandler = $onClose ? "x-on:click=\"{{ \$onClose }}\"" : 'onclick="this.closest(\'[data-modal-wrapper]\').removeAttribute(\'open\')"';
$backdropHandler = $closeable ? ($onClose ? "x-on:click=\"{{ \$onClose }}\"" : 'onclick="this.closest(\'[data-modal-wrapper]\').removeAttribute(\'open\')"') : '';
@endphp

<div
    @if ($id) id="{{ $id }}" @endif
    @if ($open) open @endif
    data-modal-wrapper
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto hidden [&[open]]:block"
    aria-modal="true"
    role="dialog"
    @if ($title) aria-labelledby="{{ $id ? $id . '-title' : 'modal-title' }}" @endif
    @if ($description) aria-describedby="{{ $id ? $id . '-description' : 'modal-description' }}" @endif
>
    {{-- 1. Backdrop (z-40: DI BAWAH kotak putih modal) --}}
    <div
        data-modal-backdrop
        class="fixed inset-0 z-40 bg-neutral-950/70 backdrop-blur-sm transition-opacity duration-fast"
        @if ($backdropHandler) @php echo $backdropHandler; @endphp @endif
        aria-hidden="true"
    ></div>

    {{-- 2. Kontainer Posisi Modal (z-50: DI ATAS backdrop) --}}
    <div
        data-modal
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
    >
        {{-- Kotak Putih Dialog --}}
        <div
            class="relative w-full {{ $sizeClass }} bg-white rounded-2xl shadow-level-4 transform transition-all duration-normal ease-standard pointer-events-auto"
            onclick="event.stopPropagation()"
        >
            @if ($title || $closeable)
                <div class="flex items-start justify-between gap-4 p-5 border-b border-neutral-100">
                    <div>
                        @if ($title)
                            <h2 id="{{ $id ? $id . '-title' : 'modal-title' }}" class="text-lg font-semibold text-neutral-900">{{ $title }}</h2>
                        @endif
                        @if ($description)
                            <p id="{{ $id ? $id . '-description' : 'modal-description' }}" class="mt-1 text-sm text-neutral-500">{{ $description }}</p>
                        @endif
                    </div>

                    @if ($closeable)
                    <button
                        type="button"
                        data-modal-close
                        class="p-2 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:visible:ring-2 focus:ring-primary-500 transition-colors"
                        @php echo $closeHandler; @endphp
                        aria-label="Tutup modal"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </div>
            @endif

            <div class="p-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>