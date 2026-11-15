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
<body class="min-h-screen bg-white font-sans antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-12">

        {{-- Panel kiri: branding --}}
        <aside class="relative hidden lg:flex lg:col-span-5 flex-col justify-between overflow-hidden bg-primary-500 p-10 xl:p-12 text-white">
            {{-- Dekorasi halus: pola titik + blob (semua CSS, tanpa aset gambar) --}}
            <div class="pointer-events-none absolute inset-0 opacity-[0.07]" aria-hidden="true"
                 style="background-image: radial-gradient(#FFFFFF 1px, transparent 1px); background-size: 22px 22px;"></div>
            <div class="pointer-events-none absolute -bottom-32 -right-24 w-96 h-96 rounded-full bg-white/5 blur-2xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -top-24 -left-16 w-72 h-72 rounded-full bg-black/5 blur-2xl" aria-hidden="true"></div>

            <div class="relative">
                {{-- Badge tahun ajaran --}}
                <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-medium tracking-wide text-white/90">
                    Tahun Ajaran 1445-1446 H
                </span>

                {{-- Logo + wordmark --}}
                <div class="mt-10 flex items-center gap-3.5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/20">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold leading-tight tracking-tight">SIMBA TAHFIDZ</p>
                        <p class="mt-0.5 text-[10px] font-medium uppercase tracking-[0.18em] text-primary-100">
                            Sistem Manajemen Belajar Al-Qur'an &amp; Tahfidz
                        </p>
                    </div>
                </div>

                <p class="mt-3 text-[11px] font-medium uppercase tracking-[0.16em] text-primary-100/80">
                    Pondok Pesantren Darut Taqwa Ponorogo
                </p>

                {{-- Sorotan fitur (ikon placeholder kosong) --}}
                <div class="mt-8 flex flex-wrap gap-2.5">
                    <div class="inline-flex items-center gap-2.5 rounded-xl border border-white/15 bg-white/5 px-3 py-2">
                        <span class="h-8 w-8 rounded-lg border border-dashed border-white/35 bg-white/5" aria-hidden="true"></span>
                        <span class="text-sm font-medium text-white/90">Terpadu &amp; Terukur</span>
                    </div>
                    <div class="inline-flex items-center gap-2.5 rounded-xl border border-white/15 bg-white/5 px-3 py-2">
                        <span class="h-8 w-8 rounded-lg border border-dashed border-white/35 bg-white/5" aria-hidden="true"></span>
                        <span class="text-sm font-medium text-white/90">30 Juz Mutqin</span>
                    </div>
                </div>
            </div>

            {{-- Kartu kutipan --}}
            <div class="relative mt-10 rounded-2xl border border-white/10 bg-neutral-950/25 p-6 backdrop-blur-sm">
                <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-accent-500">QS. Al-Qamar: 17</p>
                <p dir="rtl" lang="ar" class="mt-3 font-arabic text-[26px] leading-[2] text-white">
                    وَلَقَدْ يَسَّرْنَا الْقُرْآنَ لِلذِّكْرِ فَهَلْ مِن مُّذَّكِرٍ
                </p>
                <p class="mt-3 text-sm italic leading-relaxed text-white/80">
                    "Dan sungguh, telah Kami mudahkan Al-Qur'an untuk peringatan, maka adakah orang yang mau mengambil pelajaran?"
                </p>
                <div class="my-4 border-t border-white/10"></div>
                <p class="text-sm font-medium leading-relaxed text-white/90">
                    Membangun generasi Qur'ani yang berkarakter, mutqin &amp; bersanad.
                </p>
            </div>
        </aside>

        {{-- Panel kanan: form --}}
        <main class="relative flex min-h-screen flex-col lg:col-span-7">
            <div class="flex flex-1 items-center justify-center px-5 py-10 sm:px-8">
                <div class="w-full max-w-[420px]">
                    {{-- Brand ringkas untuk mobile --}}
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-500">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-base font-bold leading-tight text-neutral-900">SIMBA TAHFIDZ</p>
                            <p class="text-[10px] font-medium uppercase tracking-[0.16em] text-neutral-500">Darut Taqwa Ponorogo</p>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <x-alert variant="success" class="mb-4">{{ session('success') }}</x-alert>
                    @endif
                    @if (session('error'))
                        <x-alert variant="error" class="mb-4">{{ session('error') }}</x-alert>
                    @endif
                    @if (session('warning'))
                        <x-alert variant="warning" class="mb-4">{{ session('warning') }}</x-alert>
                    @endif
                    @if (session('info'))
                        <x-alert variant="info" class="mb-4">{{ session('info') }}</x-alert>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <x-alert variant="error" class="mb-4">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif

                    @yield('content')
                </div>
            </div>

            {{-- Footer --}}
            <footer class="px-5 pb-6 sm:px-8">
                <div class="mx-auto flex w-full max-w-[420px] flex-col gap-3 border-t border-neutral-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 text-xs text-neutral-500">
                        <span class="h-6 w-6 flex-shrink-0 rounded-full border border-dashed border-neutral-300" aria-hidden="true"></span>
                        <span>Kendala masuk akun? Hubungi WhatsApp Center Pesantren</span>
                    </div>
                    <span class="text-xs font-medium text-neutral-400">SIMBA v2.4</span>
                </div>
            </footer>
        </main>
    </div>
</body>
</html>