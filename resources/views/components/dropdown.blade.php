@props([
    'trigger' => null,
    'align' => 'right',
    'width' => 'w-48',
    'attributes' => [],
])

<div class="relative inline-block" data-dropdown>
    {{-- Trigger --}}
    <div class="inline-block">
        {!! $trigger ?? '<button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-primary-500 hover:bg-primary-50 active:bg-primary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2" aria-label="Menu"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg></button>' !!}
    </div>

    {{-- Dropdown Menu --}}
    <div
        class="absolute z-50 mt-2 {{ $align === 'right' ? 'right-0' : 'left-0' }} {{ $width }} bg-white rounded-xl border border-neutral-200 shadow-level-3 py-1.5 opacity-0 invisible transform transition-all duration-fast ease-standard {{ $align === 'right' ? 'translate-x-0' : 'translate-x-0' }} pointer-events-none"
        role="menu"
        aria-orientation="vertical"
    >
        {{ $slot }}
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        document.querySelectorAll('[data-dropdown]').forEach(function(dropdown) {
            const menu = dropdown.querySelector('[role="menu"]');
            const trigger = dropdown.querySelector('div:first-child');
            if (!dropdown.contains(e.target)) {
                menu.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                menu.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
            }
        });

        if (e.target.closest('[data-dropdown] > div:first-child')) {
            const dropdown = e.target.closest('[data-dropdown]');
            const menu = dropdown.querySelector('[role="menu"]');
            document.querySelectorAll('[data-dropdown]').forEach(function(d) {
                if (d !== dropdown) {
                    const m = d.querySelector('[role="menu"]');
                    m.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                    m.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
                }
            });
            menu.classList.toggle('opacity-0');
            menu.classList.toggle('invisible');
            menu.classList.toggle('pointer-events-none');
            menu.classList.toggle('opacity-100');
            menu.classList.toggle('visible');
            menu.classList.toggle('pointer-events-auto');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-dropdown] [role="menu"]').forEach(function(menu) {
                menu.classList.add('opacity-0', 'invisible', 'pointer-events-none');
                menu.classList.remove('opacity-100', 'visible', 'pointer-events-auto');
            });
        }
    });
</script>