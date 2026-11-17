<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIMBA TAHFIDZ' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&family=Amiri:wght@400;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-50 font-sans antialiased">
@php
    $user = auth()->user();
    $role = $user->role ?? null;

    $dashboardUrl = match ($role) {
        'admin' => '/admin/dashboard',
        'ustadz' => '/ustadz/dashboard',
        'kepala_tahfidz' => '/kepala/dashboard',
        'wali' => '/wali/mutabaah',
        default => '/login',
    };

    $iconQuran = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>';
    $iconUsers = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm0 0a4 4 0 014 4v1h-8v-1a4 4 0 014-4z"/></svg>';
    $iconCheck = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $iconClock = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $iconGrid = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>';
    $iconChart = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>';
    $iconList = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>';
    $iconPrinter = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h2a2 2 0 002-2v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>';
    $iconDash = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>';
    $iconBook = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12a4 4 0 00-4 4H8a4 4 0 00-4-4V4a2 2 0 012-2h8a2 2 0 012 2z"/></svg>';

    // Sidebar: hanya admin, ustadz, kepala. Wali pakai bottom bar.
    $navigation = match ($role) {
        'admin' => [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'active' => 'admin/dashboard', 'icon' => $iconDash],
            ['label' => 'Data Santri', 'url' => '/admin/santris', 'active' => 'admin/santris', 'icon' => $iconUsers],
            ['label' => 'Data Wali', 'url' => '/admin/walis', 'active' => 'admin/walis', 'icon' => $iconUsers],
            ['label' => 'Data Ustadz', 'url' => '/admin/ustadzs', 'active' => 'admin/ustadzs', 'icon' => $iconUsers],
            ['label' => 'Kelompok Halaqah', 'url' => '/admin/kelompoks', 'active' => 'admin/kelompoks', 'icon' => $iconGrid],
            ['label' => 'Angkatan Kelas', 'url' => '/admin/angkatans', 'active' => 'admin/angkatans', 'icon' => $iconBook],
        ],
        'ustadz' => [
            ['label' => 'Dasbor Halaqah', 'url' => '/ustadz/dashboard', 'active' => 'ustadz/dashboard', 'icon' => $iconDash],
            ['label' => 'Input Setoran', 'url' => '/ustadz/setorans', 'active' => 'ustadz.setorans.index', 'icon' => $iconList],
            ['label' => 'Absensi Santri', 'url' => '/ustadz/absensis', 'active' => 'ustadz.absensis', 'icon' => $iconCheck],
            ['label' => 'Rekap Capaian', 'url' => '/ustadz/rekap', 'active' => 'ustadz.rekap.index', 'icon' => $iconChart],
            ['label' => 'Cetak Rapor', 'url' => '/ustadz/rapor', 'active' => 'ustadz.rapor.index', 'icon' => $iconPrinter],
            ['label' => 'Murajaah Bulanan', 'url' => '/ustadz/murajaahs', 'active' => 'ustadz.murajaahs.index', 'icon' => $iconClock],
        ],
        'kepala_tahfidz' => [
            ['label' => 'Dashboard', 'url' => '/kepala/dashboard', 'active' => 'kepala/dashboard', 'icon' => $iconDash],
        ],
        default => [],
    };

    // Bottom bar mobile: diisi untuk ustadz
    $bottomNav = $role === 'ustadz' ? [
        ['label' => 'Dasbor', 'url' => '/ustadz/dashboard', 'active' => 'ustadz/dashboard', 'icon' => $iconDash],
        ['label' => 'Presensi', 'url' => '/ustadz/absensis', 'active' => 'ustadz.absensis', 'icon' => $iconCheck],
        ['label' => 'Setoran', 'url' => '/ustadz/setorans', 'active' => 'ustadz.setorans.index', 'icon' => $iconList],
        ['label' => 'Rekap', 'url' => '/ustadz/rekap', 'active' => 'ustadz.rekap.index', 'icon' => $iconChart],
        ['label' => 'Rapor', 'url' => '/ustadz/rapor', 'active' => 'ustadz.rapor.index', 'icon' => $iconPrinter],
    ] : [];

    $hasSidebar = $role && $role !== 'wali';
    $mainMargin = $hasSidebar ? 'lg:ml-64' : '';
    $bottomPadding = $bottomNav ? 'pb-24' : 'pb-8';
@endphp

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 z-50 px-4 py-2 bg-primary-500 text-white rounded-lg">Lompat ke konten utama</a>

    @if ($hasSidebar)
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-neutral-200 transform transition-transform duration-normal ease-standard lg:translate-x-0 -translate-x-full" aria-label="Navigasi utama">
            <div class="flex flex-col h-full">
                <div class="p-5 border-b border-neutral-100">
                    <a href="{{ $dashboardUrl }}" class="flex items-center gap-3" aria-label="SIMBA TAHFIDZ - Beranda">
                        <div class="w-10 h-10 rounded-xl bg-primary-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <span class="text-base font-bold text-neutral-900">SIMBA TAHFIDZ</span>
                    </a>
                </div>

                <nav class="flex-1 p-4 space-y-1 overflow-y-auto" aria-label="Menu utama">
                    @foreach ($navigation as $item)
                        <a href="{{ $item['url'] }}"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->is($item['active']) || request()->is($item['active'] . '/*') ? 'bg-primary-50 text-primary-600' : 'text-neutral-600 hover:text-neutral-900 hover:bg-neutral-50' }}"
                           @if (request()->is($item['active']) || request()->is($item['active'] . '/*')) aria-current="page" @endif>
                            {!! $item['icon'] !!}
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="p-4 border-t border-neutral-100">
                    <div class="flex items-center gap-3">
                        <x-avatar :name="$user->name" size="md" :photo="$user->foto ?? null" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-neutral-900 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-neutral-500 capitalize">{{ str_replace('_', ' ', $role) }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <x-button variant="outline" size="sm" full-width type="submit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </x-button>
                    </form>
                </div>
            </div>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-neutral-950/50 hidden lg:hidden" aria-hidden="true"></div>
    @endif

    <header class="sticky top-0 z-20 bg-white border-b border-neutral-200 {{ $mainMargin }}">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6">
            @if ($hasSidebar)
                <button type="button" id="sidebar-toggle" class="lg:hidden p-2 rounded-lg text-neutral-500 hover:text-neutral-700 hover:bg-neutral-100" aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            @endif

            <h1 class="text-base sm:text-lg font-semibold text-neutral-900 truncate">{{ $pageTitle ?? '' }}</h1>

            <div class="flex items-center gap-2 sm:gap-3 ml-auto">
                @if ($role === 'admin')
                    <x-button variant="ghost" size="sm" href="{{ route('admin.profile.edit') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12a4 4 0 100-8 4 4 0 000 8zm7 8a7 7 0 00-14 0"/></svg>
                        <span class="hidden sm:inline">Profil</span>
                    </x-button>
                @endif
                @if ($role === 'wali')
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-neutral-900 leading-tight">{{ $user->name }}</p>
                        <p class="text-xs text-neutral-500">Wali Santri</p>
                    </div>
                    <x-avatar :name="$user->name" size="sm" :photo="$user->foto ?? null" />
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <x-button variant="ghost" size="sm" type="submit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </x-button>
                </form>
            </div>
        </div>
    </header>

    @if ($bottomNav)
        <nav class="fixed bottom-0 left-0 right-0 z-20 bg-white border-t border-neutral-200 lg:hidden" style="padding-bottom: env(safe-area-inset-bottom)" aria-label="Navigasi mobile">
            <div class="grid grid-cols-5 gap-1 px-2 py-1">
                @foreach ($bottomNav as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex flex-col items-center gap-0.5 px-1 py-2 rounded-xl min-h-[48px] justify-center transition-colors {{ request()->is($item['active']) || request()->is($item['active'] . '/*') ? 'bg-primary-50 text-primary-600' : 'text-neutral-500 hover:text-neutral-700' }}"
                       @if (request()->is($item['active']) || request()->is($item['active'] . '/*')) aria-current="page" @endif>
                        {!! $item['icon'] !!}
                        <span class="text-[11px] font-medium">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    @endif

    <main id="main-content" class="pt-16 {{ $bottomPadding }} {{ $mainMargin }} min-h-screen">
        <div class="p-4 sm:p-6">
            @yield('content')
        </div>
    </main>

    @if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
        <div class="fixed top-20 right-4 left-4 sm:left-auto sm:right-6 z-30 flex max-w-md flex-col gap-3 pointer-events-none" aria-live="polite">
            @if (session('success'))
                <x-alert variant="success" class="pointer-events-auto animate-notification-in">{{ session('success') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert variant="error" class="pointer-events-auto animate-notification-in">{{ session('error') }}</x-alert>
            @endif
            @if (session('warning'))
                <x-alert variant="warning" class="pointer-events-auto animate-notification-in">{{ session('warning') }}</x-alert>
            @endif
            @if (session('info'))
                <x-alert variant="info" class="pointer-events-auto animate-notification-in">{{ session('info') }}</x-alert>
            @endif
            @if ($errors->any())
                <x-alert variant="error" title="Terjadi kesalahan" class="pointer-events-auto animate-notification-in">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif
        </div>
    @endif

    <footer class="border-t border-neutral-200 bg-white {{ $mainMargin }}">
        <div class="p-4 sm:p-6 text-center text-xs text-neutral-500">
            &copy; {{ date('Y') }} SIMBA TAHFIDZ - Pondok Pesantren Darut Taqwa Ponorogo
        </div>
    </footer>

    @yield('scripts')
    @stack('scripts')
</body>
</html>