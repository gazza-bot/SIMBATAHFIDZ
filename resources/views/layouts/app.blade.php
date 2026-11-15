<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIMBA TAHFIDZ' }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&family=Amiri:wght@400;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-50 font-sans antialiased">
    {{-- Skip Link --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-primary-500 text-white rounded-lg">Skip to main content</a>

    @if (auth()->check() && auth()->user()->role !== 'wali')
        {{-- Desktop Sidebar --}}
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-neutral-200 transform transition-transform duration-normal ease-standard lg:translate-x-0 -translate-x-full" aria-label="Navigasi utama">
            <div class="flex flex-col h-full">
                {{-- Logo --}}
                <div class="p-5 border-b border-neutral-100">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3" aria-label="SIMBA TAHFIDZ - Beranda">
                        <div class="w-10 h-10 rounded-xl bg-primary-500 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                        </div>
                        <span class="text-lg font-bold text-neutral-900">SIMBA TAHFIDZ</span>
                    </a>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto" role="navigation" aria-label="Menu utama">
                    @foreach ($navigation ?? [] as $item)
                        @if (isset($item['children']))
                            <details class="group">
                                <summary class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50 rounded-lg cursor-pointer list-none">
                                    {{ $item['icon'] ?? '' }}
                                    {{ $item['label'] }}
                                    <svg class="ml-auto w-4 h-4 text-neutral-400 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </summary>
                                <ul class="mt-1 ml-6 space-y-1 pl-2 border-l border-neutral-100">
                                    @foreach ($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['url'] }}" class="flex items-center gap-2 px-3 py-2 text-sm text-neutral-500 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                               {{ request()->is($child['active'] ?? '') ? 'aria-current="page"' : '' }}>
                                                {{ $child['label'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @else
                            <a href="{{ $item['url'] }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                                   {{ request()->is($item['active'] ?? '') ? 'bg-primary-50 text-primary-600' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50' }}"
                               {{ request()->is($item['active'] ?? '') ? 'aria-current="page"' : '' }}>
                                {{ $item['icon'] ?? '' }}
                                {{ $item['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>

                {{-- User Profile --}}
                <div class="p-4 border-t border-neutral-100">
                    <div class="flex items-center gap-3">
                        <x-avatar :name="auth()->user()->name" size="md" :photo="auth()->user()->foto ?? null" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-neutral-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-neutral-500 capitalize">{{ auth()->user()->role }}</p>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <x-button variant="ghost" size="sm" class="flex-1" wire:click="goToProfile">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil
                        </x-button>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <x-button variant="ghost" size="sm" class="flex-1" type="submit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </x-button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Sidebar Overlay --}}
        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-neutral-950/50 hidden lg:hidden" aria-hidden="true"></div>
    @endif

    {{-- Top Bar --}}
    <header class="sticky top-0 z-20 bg-white border-b border-neutral-200 lg:ml-64">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6">
            @if (auth()->check() && auth()->user()->role !== 'wali')
                <button type="button" id="sidebar-toggle" class="lg:hidden p-2 rounded-lg text-neutral-500 hover:text-neutral-700 hover:bg-neutral-100" aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            @endif

            <div class="flex-1 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-neutral-900">{{ $pageTitle ?? '' }}</h1>

                <div class="flex items-center gap-3 ml-auto">
                    {{-- Notifications --}}
                    <div class="relative">
                        <button type="button" class="relative p-2 rounded-lg text-neutral-500 hover:text-neutral-700 hover:bg-neutral-100" aria-label="Notifikasi">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-error-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
                        </button>
                    </div>

                    {{-- User Menu (Wali) --}}
                    @if (auth()->check() && auth()->user()->role === 'wali')
                        <x-dropdown>
                            <x-slot name="trigger">
                                <x-button variant="ghost" size="sm" class="gap-2">
                                    <x-avatar :name="auth()->user()->name" size="sm" :photo="auth()->user()->foto ?? null" />
                                    <span class="hidden sm:block text-sm font-medium text-neutral-700">{{ auth()->user()->name }}</span>
                                </x-button>
                            </x-slot>

                            <x-dropdown-item href="{{ route('wali.profile') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil
                            </x-dropdown-item>
                            <x-dropdown-item href="{{ route('wali.settings') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Pengaturan
                            </x-dropdown-item>
                            <x-dropdown-item danger onclick="document.getElementById('logout-form').submit()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </x-dropdown-item>
                        </x-dropdown>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </header>

    {{-- Mobile Bottom Navigation (Wali/Ustadz) --}}
    @if (auth()->check() && in_array(auth()->user()->role, ['ustadz', 'wali']))
        <nav class="fixed bottom-0 left-0 right-0 z-20 bg-white border-t border-neutral-200 lg:hidden" role="navigation" aria-label="Navigasi mobile">
            <div class="grid grid-cols-{{ auth()->user()->role === 'ustadz' ? 5 : 3 }} gap-1 px-2 pb-2 pt-1">
                @foreach ($bottomNav ?? [] as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex flex-col items-center gap-1 px-2 py-2 rounded-xl transition-colors
                              {{ request()->is($item['active'] ?? '') ? 'bg-primary-50 text-primary-600' : 'text-neutral-500 hover:text-neutral-700' }}"
                       {{ request()->is($item['active'] ?? '') ? 'aria-current="page"' : '' }}>
                        {{ $item['icon'] ?? '' }}
                        <span class="text-xs font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    @endif

    {{-- Main Content --}}
    <main id="main-content" class="pt-16 pb-{{ auth()->check() && in_array(auth()->user()->role, ['ustadz', 'wali']) ? '24' : '8' }} lg:ml-64 min-h-screen">
        <div class="p-4 sm:p-6 lg:p-8">
            {{-- Flash Messages --}}
            @if (session('success'))
                <x-alert variant="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert variant="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif
            @if (session('warning'))
                <x-alert variant="warning" class="mb-6">{{ session('warning') }}</x-alert>
            @endif
            @if (session('info'))
                <x-alert variant="info" class="mb-6">{{ session('info') }}</x-alert>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <x-alert variant="error" class="mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            {{ $slot }}
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-neutral-200 lg:ml-64">
        <div class="p-4 sm:p-6 text-center text-sm text-neutral-500">
            &copy; {{ date('Y') }} SIMBA TAHFIDZ - Pondok Pesantren Darut Taqwa Ponorogo
        </div>
    </footer>
</body>
</html>