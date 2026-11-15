@props([
    'tabs' => [],
    'active' => null,
    'variant' => 'default',
    'class' => '',
    'attributes' => [],
])

<?php
$variants = [
    'default' => 'border-b border-neutral-200',
    'underline' => 'border-b border-neutral-200',
    'pills' => 'gap-1 bg-neutral-100 p-1 rounded-xl',
];
$variantClass = $variants[$variant] ?? $variants['default'];
$activeTab = $active ?? ($tabs[0]['key'] ?? null);
?>

<div {{ $attributes->merge(['class' => $attributes->get('class', '')]) }}>
    <nav class="{{ $variantClass }} {{ $class }}" role="tablist" aria-label="Tab navigation">
        @foreach ($tabs as $index => $tab)
            @php
                $tabKey = $tab['key'] ?? $index;
                $isActive = $tabKey === $activeTab;
                $tabClasses = 'px-4 py-2.5 text-sm font-medium transition-all duration-fast ease-standard focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-lg';
                
                if ($variant === 'pills') {
                    $tabClasses .= $isActive ? ' bg-white text-primary-600 shadow-level-1' : ' text-neutral-600 hover:text-neutral-900 hover:bg-neutral-200';
                } elseif ($variant === 'underline') {
                    $tabClasses .= $isActive ? ' text-primary-600 border-b-2 border-primary-600 -mb-px' : ' text-neutral-500 hover:text-neutral-700';
                } else {
                    $tabClasses .= $isActive ? ' text-primary-600 border-b-2 border-primary-600 -mb-px' : ' text-neutral-500 hover:text-neutral-700';
                }
            @endphp
            <button
                type="button"
                role="tab"
                aria-selected="{{ $isActive ? 'true' : 'false' }}"
                aria-controls="panel-{{ $tabKey }}"
                id="tab-{{ $tabKey }}"
                class="{{ $tabClasses }}"
                wire:click="setActiveTab('{{ $tabKey }}')"
                @if (isset($tab['onclick'])) onclick="{{ $tab['onclick'] }}" @endif
            >
                @if (isset($tab['icon']))
                    <span class="inline-flex items-center justify-center w-5 h-5 mr-2" aria-hidden="true">{{ $tab['icon'] }}</span>
                @endif
                {{ $tab['label'] ?? 'Tab' }}
                @if (isset($tab['badge']))
                    <x-badge variant="{{ $tab['badge']['variant'] ?? 'primary' }}" size="xs">{{ $tab['badge']['value'] }}</x-badge>
                @endif
            </button>
        @endforeach
    </nav>

    <div class="mt-4">
        @foreach ($tabs as $index => $tab)
            @php
                $tabKey = $tab['key'] ?? $index;
                $isActive = $tabKey === $activeTab;
            @endphp
            <div
                role="tabpanel"
                id="panel-{{ $tabKey }}"
                aria-labelledby="tab-{{ $tabKey }}"
                class="{{ $isActive ? '' : 'hidden' }}"
            >
                {{ $tab['content'] ?? $slot }}
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('keydown', function(e) {
        if (e.target.role === 'tab') {
            const tabs = Array.from(document.querySelectorAll('[role="tab"]'));
            const currentIndex = tabs.indexOf(e.target);
            let nextIndex = currentIndex;
            
            if (e.key === 'ArrowRight') nextIndex = (currentIndex + 1) % tabs.length;
            if (e.key === 'ArrowLeft') nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
            if (e.key === 'Home') nextIndex = 0;
            if (e.key === 'End') nextIndex = tabs.length - 1;
            
            if (nextIndex !== currentIndex) {
                e.preventDefault();
                tabs[nextIndex].click();
                tabs[nextIndex].focus();
            }
        }
    });
</script>
@endpush