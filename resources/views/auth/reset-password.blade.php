@extends('layouts.guest', ['title' => $title])

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight text-neutral-900 sm:text-[26px]">Atur Ulang Kata Sandi</h1>
    <p class="mt-2 text-sm leading-relaxed text-neutral-500">
        Masukkan kata sandi baru Anda. Kata sandi minimal 8 karakter.
    </p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="username" value="{{ $username }}">

    {{-- Password --}}
    <x-input
        name="password"
        id="password"
        label="Kata Sandi Baru"
        placeholder="Masukkan kata sandi baru"
        type="password"
        autocomplete="new-password"
        required
        :error="$errors->first('password')"
        iconLeft="
            <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6'/>
            </svg>
        "
    />

    {{-- Password Confirmation --}}
    <x-input
        name="password_confirmation"
        id="password_confirmation"
        label="Konfirmasi Kata Sandi Baru"
        placeholder="Masukkan kembali kata sandi baru"
        type="password"
        autocomplete="new-password"
        required
        :error="$errors->first('password_confirmation')"
        iconLeft="
            <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6'/>
            </svg>
        "
    />

    {{-- Submit --}}
    <x-button type="submit" variant="primary" size="lg" full-width class="mt-2">
        Atur Ulang Kata Sandi
    </x-button>
</form>

<div class="mt-6 text-center">
    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
        Kirim ulang permintaan
    </a>
    <span class="mx-2 text-neutral-400">|</span>
    <a href="{{ route('login') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
        Kembali ke Login
    </a>
</div>
@endsection