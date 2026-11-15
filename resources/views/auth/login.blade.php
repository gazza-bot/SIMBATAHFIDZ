@extends('layouts.guest', ['title' => $title])

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900 sm:text-[26px]">Masuk ke Akun Anda</h1>
        <p class="mt-2 text-sm leading-relaxed text-neutral-500">
            Silakan masukkan identitas akun dan kata sandi Anda untuk mengakses sistem pembelajaran.
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Username --}}
        <x-input
            name="username"
            id="username"
            label="Username / Email / ID Pengguna"
            placeholder="Masukkan NIP, NIS, atau Username"
            type="text"
            autocomplete="username"
            required
            autofocus
            :value="old('username')"
            :error="$errors->first('username')"
            iconLeft="
                <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'/>
                </svg>
            "
        />

        {{-- Password --}}
        <x-input
            name="password"
            id="password"
            label="Kata Sandi"
            placeholder="Masukkan kata sandi"
            type="password"
            autocomplete="current-password"
            required
            :error="$errors->first('password')"
            iconLeft="
                <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6'/>
                </svg>
            "
        />

        {{-- Remember & Forgot --}}
        <div class="flex items-center justify-between">
            <label class="flex cursor-pointer items-center gap-2">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-neutral-300 text-primary-500 focus:ring-primary-500">
                <span class="text-sm text-neutral-600">Ingat Saya</span>
            </label>

            <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                Lupa Kata Sandi?
            </a>
        </div>

        {{-- Submit --}}
        <x-button type="submit" variant="primary" size="lg" full-width class="mt-2">
            Masuk ke Aplikasi
        </x-button>
    </form>
@endsection