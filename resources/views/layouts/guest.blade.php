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
<body class="min-h-screen bg-neutral-50 font-sans antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3" aria-label="SIMBA TAHFIDZ">
                <div class="w-14 h-14 rounded-2xl bg-primary-500 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                </div>
            </a>
            <h1 class="mt-4 text-2xl font-bold text-neutral-900">SIMBA TAHFIDZ</h1>
            <p class="mt-1 text-neutral-500">Sistem Informasi Mutaba'ah & Belajar Al-Qur'an</p>
        </div>

        {{-- Card --}}
        <x-card variant="elevated" padding="lg" class="w-full">
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

            {{ $slot }}
        </x-card>

        {{-- Footer Links --}}
        <div class="mt-6 text-center text-sm text-neutral-500">
            <p>&copy; {{ date('Y') }} SIMBA TAHFIDZ - Pondok Pesantren Darut Taqwa Ponorogo</p>
        </div>
    </div>
</body>
</html>