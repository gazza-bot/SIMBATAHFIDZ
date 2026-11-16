@php
    $tabs = [
        ['key' => 'santris', 'label' => 'Santri', 'url' => route('admin.santris.index'), 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm0 0a4 4 0 014 4v1h-8v-1a4 4 0 014-4z"/></svg>'],
        ['key' => 'walis', 'label' => 'Wali', 'url' => route('admin.walis.index'), 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'],
        ['key' => 'ustadzs', 'label' => 'Ustadz', 'url' => route('admin.ustadzs.index'), 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'],
        ['key' => 'kelompoks', 'label' => 'Kelompok', 'url' => route('admin.kelompoks.index'), 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'],
        ['key' => 'angkatans', 'label' => 'Angkatan', 'url' => route('admin.angkatans.index'), 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'],
    ];

    $current = request()->route()->getName() ?? '';
    $active = collect($tabs)->firstWhere('url', fn($u) => str_contains($current, str_replace(route('admin.dashboard'), '', $u)))['key'] ?? 'santris';
@endphp

<div class="mb-6">
    <div class="border-b border-neutral-200" role="tablist" aria-label="Master Data Tabs">
        <nav class="flex gap-1 overflow-x-auto pb-1 relative" style="scrollbar-width: thin;">
            {{-- Active indicator bar --}}
            <div class="absolute bottom-0 left-0 h-1 bg-primary-500 transition-all duration-300 ease-out origin-left"
                 style="width: var(--active-width, 0); transform: translateX(var(--active-x, 0));"></div>

            @foreach ($tabs as $index => $tab)
                @php
                    $isActive = $tab['key'] === $active;
                @endphp
                <a
                    href="{{ $tab['url'] }}"
                    role="tab"
                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                    data-index="{{ $index }}"
                    class="relative inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap rounded-lg transition-all duration-200 ease-out {{ $isActive ? 'text-primary-600' : 'text-neutral-500 hover:text-neutral-700 hover:bg-neutral-50' }}"
                    @if ($isActive) data-active-tab @endif
                >
                    <span class="inline-flex items-center justify-center w-5 h-5" aria-hidden="true">{!! $tab['icon'] !!}</span>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.querySelector('[role="tablist"] nav');
        const indicator = nav?.querySelector('.absolute.bottom-0');
        const tabs = nav?.querySelectorAll('[data-index]');

        function updateIndicator(activeTab) {
            if (!activeTab || !indicator) return;
            const rect = activeTab.getBoundingClientRect();
            const navRect = nav.getBoundingClientRect();
            indicator.style.width = rect.width + 'px';
            indicator.style.transform = 'translateX(' + (rect.left - navRect.left) + 'px)';
        }

        tabs?.forEach(tab => {
            if (tab.hasAttribute('data-active-tab')) {
                updateIndicator(tab);
            }
            tab.addEventListener('click', () => {
                updateIndicator(tab);
            });
        });

        window.addEventListener('resize', () => {
            const activeTab = nav?.querySelector('[data-active-tab]');
            if (activeTab) updateIndicator(activeTab);
        });
    });
</script>
@endpush