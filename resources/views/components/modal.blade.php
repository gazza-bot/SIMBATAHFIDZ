@props([
    'open' => false,
    'title' => null,
    'description' => null,
    'size' => 'md',
    'closeable' => true,
    'onClose' => null,
    'attributes' => [],
])

<?php
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
?>

@if ($open)
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 z-50 bg-neutral-950/70 backdrop-blur-sm transition-opacity duration-fast"
        @if ($closeable) wire:click="$dispatch('modal-close')" @else onclick="this.closest('[data-modal]').querySelector('[data-modal-close]').click()" @endif
        aria-hidden="true"
    ></div>

    {{-- Modal --}}
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        data-modal
        role="dialog"
        aria-modal="true"
        @if ($title) aria-labelledby="modal-title" @endif
        @if ($description) aria-describedby="modal-description" @endif
    >
        <div
            class="w-full {{ $sizeClass }} bg-white rounded-2xl shadow-level-4 transform transition-all duration-normal ease-standard"
            @if ($size === 'fullscreen') class="w-full max-w-full m-0 rounded-none h-full" @endif
        >
            @if ($title || $closeable)
                <div class="flex items-start justify-between gap-4 p-5 border-b border-neutral-100">
                    <div>
                        @if ($title)
                            <h2 id="modal-title" class="text-lg font-semibold text-neutral-900">{{ $title }}</h2>
                        @endif
                        @if ($description)
                            <p id="modal-description" class="mt-1 text-sm text-neutral-500">{{ $description }}</p>
                        @endif
                    </div>
@if ($closeable)
                        <button
                            type="button"
                            data-modal-close
                            class="p-1.5 rounded-lg text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                            @if ($onClose) wire:click="{{ $onClose }}" @else onclick="this.closest('[data-modal]').remove(); document.querySelector('.fixed.inset-0.bg-neutral-950\/70')?.remove()" @endif
                            aria-label="Tutup modal"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    @endif
                </div>
            @endif

            <div class="p-5">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- Focus trap script --}}
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.querySelector('[data-modal]');
                if (modal) {
                    const closeBtn = modal.querySelector('[data-modal-close]');
                    if (closeBtn) closeBtn.click();
                }
            }
        });
    </script>
@endif