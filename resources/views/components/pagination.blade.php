@props([
    'paginator' => null,
    'simple' => false,
    'attributes' => [],
])

@if ($paginator && $paginator->lastPage() > 1)
    <nav class="flex items-center justify-center gap-1" aria-label="Pagination" {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}>
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <button type="button" class="px-3 py-1.5 text-sm font-medium text-neutral-300 bg-neutral-100 rounded-lg cursor-not-allowed" disabled aria-label="Halaman sebelumnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500" aria-label="Halaman sebelumnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        @endif

        @if ($simple)
            <span class="px-3 py-1.5 text-sm font-medium text-neutral-600">
                Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
            </span>
        @else
            {{-- Page numbers --}}
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 text-sm font-semibold text-white bg-primary-500 rounded-lg" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ $page }}</a>
                @endif
            @endforeach
        @endif

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm font-medium text-neutral-700 bg-white border border-neutral-300 rounded-lg hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-primary-500" aria-label="Halaman selanjutnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <button type="button" class="px-3 py-1.5 text-sm font-medium text-neutral-300 bg-neutral-100 rounded-lg cursor-not-allowed" disabled aria-label="Halaman selanjutnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        @endif
    </nav>
@endif