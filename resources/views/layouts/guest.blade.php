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

                {{-- Foto suasana pesantren + badge overlay --}}
                <div class="mt-8 relative">
                    {{-- Ornamen geometris dekoratif (lingkaran + kotak garis) --}}
                    <svg class="pointer-events-none absolute -top-4 -left-4 w-72 h-72 opacity-10" aria-hidden="true" viewBox="0 0 288 288" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="144" cy="144" r="130" stroke="white" stroke-width="1"/>
                        <circle cx="144" cy="144" r="90" stroke="white" stroke-width="1"/>
                        <rect x="54" y="54" width="180" height="180" rx="4" stroke="white" stroke-width="1"/>
                        <rect x="84" y="84" width="120" height="120" rx="4" stroke="white" stroke-width="1"/>
                    </svg>

                    {{-- Kartu foto --}}
                    <div class="relative rounded-2xl overflow-hidden ring-1 ring-white/10 bg-neutral-900">
                        <img
                            src="{{ asset('images/gambar_login.png') }}"
                            alt="Suasana pesantren: santri belajar Al-Qur'an di perpustakaan"
                            width="406"
                            height="176"
                            class="w-full h-auto object-cover"
                        />

                        {{-- Overlay gradien bawah agar badge terbaca --}}
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-neutral-950/90 to-transparent" aria-hidden="true"></div>

                        {{-- Badge overlay di atas foto --}}
                        <div class="absolute bottom-4 left-4 right-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-4 pb-4">
                            {{-- Badge kiri: Terpadu & Terukur --}}
                            <div class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 backdrop-blur-sm px-4 py-2.5 text-white/95">
                                <svg class="w-5 h-5 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
                                </svg>
                                <span class="text-sm font-medium tracking-wide">Terpadu & Terukur</span>
                            </div>

                            {{-- Badge kanan: 30 JUZ MUTQIN --}}
                            <div class="inline-flex items-center gap-2 rounded-xl bg-primary-800/80 px-4 py-2.5 text-white">
                                <span class="text-[10px] font-semibold uppercase tracking-[0.2em]">30 JUZ MUTQIN</span>
                            </div>
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